<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingTaxRefund extends Model
{
    protected $fillable = array("billId","receiptNumber","totalAmount","vatAmount","taxRefundAmount","taxRegisterStatus","taxRegisterTagNumber","taxRefundTagNumber","taxRefundStatus","taxRefundQrCode","refundAmount");
    public $timestamps = false;
}
