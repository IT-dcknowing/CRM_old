<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Helper\Reply;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use App\Models\Appreciation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\ProjectTimeLog;
use App\Models\DashboardWidget;
use App\Models\EmployeeDetails;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectTimeLogBreak;
use App\Models\EmployeeShiftSchedule;
use App\Models\Company;

/**
 *
 */
trait EmployeeDashboard
{
    use PilotageProjets;
    use EspaceCollaborateur;

    /**
     */
    public function employeeDashboard()
    {
        $user = user();

        // Bloc « Mon tableau de bord » conforme a dck-flow-task :
        // ses taches, sa semaine, ses projets, les annonces. Les
        // indicateurs de portefeuille restent sur l'ecran administrateur.
        $this->chargerEspaceCollaborateur($user->id);

        $completedTaskColumn = TaskboardColumn::completeColumn();
        // Le pointage (Attendance) n'a plus de bouton actif dans la vue :
        // routes attendances.* toutes retirees de routes/web.php.
        // attendance_setting() reste necessaire pour week_start_from,
        // utilise plus bas par le calendrier hebdomadaire des shifts.
        $showClockIn = attendance_setting();

        $currentDate = now();

        $this->checkJoiningDate = true;

        if (is_null(user()->employeeDetail->joining_date) || user()->employeeDetail->joining_date->gt($currentDate)) {
            $this->checkJoiningDate = false;
        }

        $this->viewEventPermission = user()->permission('view_events');
        $this->viewHolidayPermission = user()->permission('view_holiday');
        $this->viewTaskPermission = user()->permission('view_tasks');
        $this->viewTicketsPermission = user()->permission('view_tickets');
        $this->viewLeavePermission = user()->permission('view_leave');
        $this->viewNoticePermission = user()->permission('view_notice');
        $this->editTimelogPermission = user()->permission('edit_timelogs');

        // Getting Attendance setting data

        if (request('start') && request('end') && !is_null($this->viewEventPermission) && $this->viewEventPermission != 'none') {
            $eventData = array();

            $events = Event::with('attendee', 'attendee.user');

            if ($this->viewEventPermission == 'added') {
                $events->where('events.added_by', $this->user->id);
            }
            elseif ($this->viewEventPermission == 'owned' || $this->viewEventPermission == 'both') {
                $events->where('events.added_by', $this->user->id)
                    ->orWhere(function ($q) {
                        $q->whereHas('attendee.user', function ($query) {
                            $query->where('user_id', $this->user->id);
                        });
                    });
            }

            $events = $events->get();

            foreach ($events as $key => $event) {
                $eventData[] = [
                    'id' => $event->id,
                    'title' => $event->event_name,
                    'start' => $event->start_date_time,
                    'end' => $event->end_date_time,
                    'extendedProps' => ['bg_color' => $event->label_color, 'color' => '#fff'],
                ];
            }

            return $eventData;
        }

        $this->totalProjects = Project::select('projects.id')
            ->where('completion_percent', '<>', 100)
            ->join('project_members', 'project_members.project_id', '=', 'projects.id')
            ->where('project_members.user_id', $this->user->id)
            ->distinct()
            ->count('projects.id');

        $this->counts = User::without(['clientDetails', 'employeeDetail', 'roles', 'session'])->withCount([
            'timeLogs as totalHoursLogged' => function ($query) {
                $query->select(DB::raw('IFNULL(sum(total_minutes), 0)'))
                    ->where('user_id', $this->user->id);
            },
            'tasks as totalCompletedTasks' => function ($query) use ($completedTaskColumn) {
                $query->where('tasks.board_column_id', $completedTaskColumn->id)
                    ->whereHas('taskUsers', function ($query) {
                        $query->where('user_id', $this->user->id);
                    });
            }
        ])
            ->findOrFail($this->user->id);

        $tasks = $this->pendingTasks = Task::with(['activeProject', 'boardColumn', 'labels'])
            ->join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->where('task_users.user_id', $this->user->id)
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->select('tasks.*')
            ->distinct()
            ->orderBy('tasks.id', 'desc')
            ->get();

        $this->inProcessTasks = $tasks->count();

        $this->dueTasks = $tasks->filter(function ($item) {
            return !is_null($item->due_date) && $item->due_date->endOfDay()->isPast();
        })->count();

        $projects = Project::with('members')
            ->where('completion_percent', '<>', '100')
            ->leftJoin('project_members', 'project_members.project_id', 'projects.id')
            ->leftJoin('users', 'project_members.user_id', 'users.id')
            ->selectRaw('project_members.user_id, projects.deadline as due_date, projects.id')
            ->where('project_members.user_id', $this->user->id)
            ->groupBy('projects.id')
            ->get();

        $projects = $projects->whereNotNull('due_date');

        $this->dueProjects = $projects->filter(function ($value) {
            return now(company()->timezone)->gt($value->due_date);
        })->count();

        $currentDate = now(company()->timezone)->format('Y-m-d');

        // Check Holiday by date
        $this->checkTodayHoliday = Holiday::where('date', $currentDate)
            ->where(function ($query) use ($user) {
                $query->orWhere('department_id_json', 'like', '%"' . $user->employeeDetail->department_id . '"%')
                    ->orWhereNull('department_id_json');
            })
            ->where(function ($query) use ($user) {
                $query->orWhere('designation_id_json', 'like', '%"' . $user->employeeDetail->designation_id . '"%')
                    ->orWhereNull('designation_id_json');
            })
            ->where(function ($query) use ($user) {
                if (!is_Null($user->employeeDetail->employment_type)) {
                    $query->orWhere('employment_type_json', 'like', '%"' . $user->employeeDetail->employment_type . '"%')
                        ->orWhereNull('employment_type_json');
                }
            })
            ->first();

        $this->myActiveTimer = ProjectTimeLog::with('task', 'user', 'project', 'breaks', 'activeBreak')
            ->where('user_id', user()->id)
            ->whereNull('end_time')
            ->first();

        $currentDay = now(company()->timezone)->format('m-d');

        $this->upcomingBirthdays = EmployeeDetails::whereHas('user', function ($query) {
            return $query->where('status', 'active');
        })
            ->with('user')
            ->select('*', 'date_of_birth', DB::raw('MONTH(date_of_birth) months'), DB::raw('DAY(date_of_birth) as day'))
            ->whereNotNull('date_of_birth')
            ->where(function ($query) use ($currentDay) {
                $query->whereRaw('DATE_FORMAT(`date_of_birth`, "%m-%d") >= "' . $currentDay . '"')->orderBy('date_of_birth');
            })
            ->limit('5')
            ->orderBy('months')
            ->orderBy('day')
            ->get()->values()->all();

        $now = now(company()->timezone);
        $this->weekStartDate = $now->copy()->startOfWeek($showClockIn->week_start_from);
        $this->weekEndDate = $this->weekStartDate->copy()->addDays(14);
        $this->weekPeriod = CarbonPeriod::create($this->weekStartDate, $this->weekStartDate->copy()->addDays(6)); // Get All Dates from start to end date

        $this->employeeShifts = EmployeeShiftSchedule::where('user_id', user()->id)
            ->whereBetween(DB::raw('DATE(`date`)'), [$this->weekStartDate->format('Y-m-d'), $this->weekEndDate->format('Y-m-d')])
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as dates'), 'employee_shift_schedules.*')
            ->with('shift', 'user', 'requestChange')
            ->get();

        $this->employeeShiftDates = $this->employeeShifts->pluck('dates')->toArray();

        $currentWeekDates = [];
        $weekShifts = [];

        $weekHolidays = Holiday::whereBetween(DB::raw('DATE(`date`)'), [$this->weekStartDate->format('Y-m-d'), $this->weekEndDate->format('Y-m-d')])
            ->where(function ($query) use ($user) {
                $query->where(function ($subquery) use ($user) {
                    $subquery->where(function ($q) use ($user) {
                        $q->where('department_id_json', 'like', '%"' . $user->employeeDetails->department_id . '"%')
                            ->orWhereNull('department_id_json');
                    });
                    $subquery->where(function ($q) use ($user) {
                        $q->where('designation_id_json', 'like', '%"' . $user->employeeDetails->designation_id . '"%')
                            ->orWhereNull('designation_id_json');
                    });
                    $subquery->where(function ($q) use ($user) {
                        $q->where('employment_type_json', 'like', '%"' . $user->employeeDetails->employment_type . '"%')
                            ->orWhereNull('employment_type_json');
                    });
                });
            })
            ->select(DB::raw('DATE_FORMAT(`date`, "%Y-%m-%d") as hdate'), 'occassion')
            ->get();

        $holidayDates = $weekHolidays->pluck('hdate')->toArray();
        $generalShift = Company::with(['attendanceSetting', 'attendanceSetting.shift'])->first();

        // phpcs:ignore
        for ($i = $this->weekStartDate->copy(); $i < $this->weekEndDate->copy(); $i->addDay()) {
            $date = Carbon::parse($i);
            array_push($currentWeekDates, $date);

            if (in_array($date->toDateString(), $holidayDates)) {

                $leave = [];

                foreach ($weekHolidays as $holiday) {
                    if ($holiday->hdate == $date->toDateString()) {
                        $leave = '<i class="fa fa-star text-warning"></i> ' . $holiday->occassion;
                    }
                }

                array_push($weekShifts, $leave);

            }
            elseif (in_array($date->toDateString(), $this->employeeShiftDates)) {
                $shiftSchedule = [];

                foreach ($this->employeeShifts as $shift) {
                    if ($shift->dates == $date->toDateString()) {
                        $shiftSchedule = $shift;
                    }
                }

                array_push($weekShifts, $shiftSchedule);

            }
            else {
                $defaultShift = ($generalShift && $generalShift->attendanceSetting && $generalShift->attendanceSetting->shift) ? '<span class="badge badge-primary" style="background-color:' . $generalShift->attendanceSetting->shift->color . '">' . $generalShift->attendanceSetting->shift->shift_name . '</span>' : '--';
                array_push($weekShifts, $defaultShift);
            }

        }

        $this->upcomingAnniversaries = EmployeeDetails::whereHas('user', function ($query) {
            return $query->where('status', 'active');
        })
            ->with('user')
            ->select('employee_details.id', 'employee_details.user_id', 'employee_details.joining_date', DB::raw('MONTH(joining_date) months'), DB::raw('DAY(joining_date) as day'))
            ->whereNotNull('joining_date')
            ->where(function ($query) use ($currentDay) {
                $query->whereRaw('DATE_FORMAT(`joining_date`, "%m-%d") = "' . $currentDay . '"')->orderBy('joining_date');
            })
            ->orderBy('months')
            ->orderBy('day')
            ->get()->values()->all();

        $this->currentWeekDates = $currentWeekDates;
        $this->weekShifts = $weekShifts;
        $this->event_filter = explode(',', user()->employeeDetail->calendar_view);
        $this->widgets = DashboardWidget::where('dashboard_type', 'private-dashboard')->get();
        $this->activeWidgets = $this->widgets->filter(function ($value, $key) {
            return $value->status == '1';
        })->pluck('widget_name')->toArray();

        $this->dateWiseTimelogs = ProjectTimeLog::dateWiseTimelogs(now()->toDateString(), user()->id);
        $this->dateWiseTimelogBreak = ProjectTimeLogBreak::dateWiseTimelogBreak(now()->toDateString(), user()->id);

        $this->weekWiseTimelogs = ProjectTimeLog::weekWiseTimelogs($this->weekStartDate->copy()->toDateString(), $this->weekEndDate->copy()->toDateString(), user()->id);
        $this->weekWiseTimelogBreak = ProjectTimeLogBreak::weekWiseTimelogBreak($this->weekStartDate->toDateString(), $this->weekEndDate->toDateString(), user()->id);

        $this->dayMinutes = $this->dateWiseTimelogs->sum('total_minutes');
        $this->dayBreakMinutes = $this->dateWiseTimelogBreak->sum('total_minutes');
        $loggedMinutes = $this->dayMinutes - $this->dayBreakMinutes;

        $this->totalDayMinutes = $this->formatTime($loggedMinutes);
        $this->totalDayBreakMinutes = $this->formatTime($this->dayBreakMinutes);

        $this->appreciations = Appreciation::with(['award', 'award.awardIcon'])
            ->with(['awardTo.employeeDetail', 'awardTo.employeeDetail.designation:id,name'])
            ->orderByDesc('award_date')
            ->latest()
            ->limit(5)
            ->get();

        $currentDay = now(company()->timezone)->format('Y-m-d');

        $this->employees = EmployeeDetails::with('user')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->without('clientDetails');

        if (in_array('admin', user_roles())) {

            $employees = $this->employees->clone()
                ->where(function ($query) use ($currentDay) {
                    $query->whereNotNull('notice_period_end_date')
                        ->where('notice_period_end_date', '>=', $currentDay)
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('probation_end_date')
                                ->where('probation_end_date', '>=', $currentDay);
                        })
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('internship_end_date')
                                ->where('internship_end_date', '>=', $currentDay);
                        })
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('contract_end_date')
                                ->where('contract_end_date', '>=', $currentDay);
                        });
                })
                ->orderBy('notice_period_end_date', 'asc')
                ->orderBy('probation_end_date', 'asc')
                ->orderBy('internship_end_date', 'asc')
                ->orderBy('contract_end_date', 'asc')
                ->get();

            $this->noticePeriod = $employees->filter(function ($employee) {
                return $employee->notice_period_end_date !== null;
            })->sortBy('notice_period_end_date')->values();

            $this->probations = $employees->filter(function ($employee) {
                return $employee->probation_end_date !== null;
            })->sortBy('probation_end_date')->values();

            $this->internships = $employees->filter(function ($employee) {
                return $employee->internship_end_date !== null;
            })->sortBy('internship_end_date')->values();

            $this->contracts = $employees->filter(function ($employee) {
                return $employee->contract_end_date !== null;
            })->sortBy('contract_end_date')->values();

        }
        else {
            $userId = user()->id;
            $employee = $this->employees->clone()
                ->where('user_id', $userId)
                ->where(function ($query) use ($currentDay) {
                    $query->whereNotNull('notice_period_end_date')
                        ->where('notice_period_end_date', '>=', $currentDay)
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('probation_end_date')
                                ->where('probation_end_date', '>=', $currentDay);
                        })
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('internship_end_date')
                                ->where('internship_end_date', '>=', $currentDay);
                        })
                        ->orWhere(function ($query) use ($currentDay) {
                            $query->whereNotNull('contract_end_date')
                                ->where('contract_end_date', '>=', $currentDay);
                        });
                })
                ->first();

            $this->noticePeriod = $employee && $employee->notice_period_end_date && $employee->notice_period_end_date >= $currentDay ? $employee : null;

            $this->probation = $employee && $employee->probation_end_date && $employee->probation_end_date >= $currentDay ? $employee : null;

            $this->internship = $employee && $employee->internship_end_date && $employee->internship_end_date >= $currentDay ? $employee : null;

            $this->contract = $employee && $employee->contract_end_date && $employee->contract_end_date >= $currentDay ? $employee : null;

        }

        return view('dashboard.employee.index', $this->data);
    }

    private function formatTime($totalMinutes)
    {
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return $hours > 0
            ? $hours . 'h' . ($minutes > 0 ? ' ' . sprintf('%02dm', $minutes) : '')
            : ($minutes > 0 ? sprintf('%dm', $minutes) : '0s');
    }

}
