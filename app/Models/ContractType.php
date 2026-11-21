<?php

namespace App\Models;

use App\Traits\HasCompany;

/**
 * App\Models\ContractType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereUpdatedAt($value)
 * @property int|null $company_id
 * @property-read \App\Models\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereCompanyId($value)
 * @mixin \Eloquent
 */
class ContractType extends BaseModel
{

    use HasCompany;

    /**
     * Reconnait le type "Nouvelle adhesion" a partir de son libelle.
     * Point unique de detection : a remplacer par un code stable si
     * le libelle venait a changer.
     */
    public function estNouvelleAdhesion(): bool
    {
        $nom = mb_strtolower((string) $this->name);

        return str_contains($nom, 'nouvelle') && str_contains($nom, 'adh');
    }
}
