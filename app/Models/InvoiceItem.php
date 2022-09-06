<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**Fetches all Categories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllPublicationUploads()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
    }

    public static function addInvoiceItems(int $invoice_id, array $field)
    {
        $newJobPost = new self();
        $newJobPost->name = $field['name'];
        $newJobPost->amount = $field['amount'];
        $newJobPost->quantity = $field['quantity'];
        $newJobPost->rate = $field['rate'];
        $newJobPost->invoice_id = $invoice_id;
        $newJobPost->save();
    }

    public function updateInvoiceItemsWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'title' => $fields["title"],
                'company_name' => $fields["company_name"],
                'location_id' => $fields["location_id"],
                'job_type_id' => $fields["job_type_id"],
                'description' => $fields["description"],
                'responsibilities' => $fields["responsibilities"],
                'requirements' => $fields["requirements"],
                'summaries' => $fields["summaries"]
            ]);
    }
}
