<?php

namespace App\Http\Requests;

use App\Enums\SchoolMembershipStatus;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreLibraryLoanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'barcode' => [
                'required', 'string', 'max:60',
                ValidationRule::exists('library_copies', 'barcode')->where(
                    fn (Builder $query) => $query->where('school_id', current_school_id()),
                ),
            ],
            'user_id' => [
                'required', 'integer',
                ValidationRule::exists('school_memberships', 'user_id')->where(
                    fn (Builder $query) => $query
                        ->where('school_id', current_school_id())
                        ->where('status', SchoolMembershipStatus::Active->value),
                ),
            ],
        ];
    }

    /**
     * Get the messages the desk reads when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'barcode.exists' => 'No copy on this campus carries that barcode.',
            'user_id.exists' => 'That person does not belong to this campus.',
        ];
    }
}
