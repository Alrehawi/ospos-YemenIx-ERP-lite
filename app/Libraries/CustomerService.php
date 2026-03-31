<?php

namespace App\Libraries;

use App\Models\Customer;
use App\Models\Accounting_model;
use Config\Database;
class CustomerService
{
    protected $customer;
    protected $accounting;
    protected $db;
    public function __construct()
    {
        $this->customer = model(Customer::class);
        $this->accounting = model(Accounting_model::class);
        $this->db = Database::connect();
    }

    // ============================
    // 🔥 إنشاء عميل + حساب محاسبي
    // ============================
   public function save_customer_with_account($person_data, $customer_data, $customer_id = NEW_ENTRY)
    {
        // 1. حفظ العميل
        $success = $this->customer->save_customer($person_data, $customer_data, $customer_id);

        if (!$success) {
            return false;
        }

        // 2. الحصول على customer_id الصحيح
        if ($customer_id == NEW_ENTRY)
        {
            // 🔥 الأفضل: جلب آخر ID من DB مباشرة
            $customer_id_final = $this->db->insertID();
        }
        else
        {
            $customer_id_final = $customer_id;
        }

        // 3. 🔥 تأكد من وجود account_number
        $customer_row = $this->db->table('ospos_customers')
            ->select('account_number')
            ->where('person_id', $customer_id_final)
            ->get()
            ->getRowArray();

        $account_code = trim($customer_row['account_number'] ?? '');

        // 🔥 إذا فارغ أو رقم فقط → أنشئه
        if (empty($account_code) || is_numeric($account_code))
        {
            $account_code = 'C' . str_pad($customer_id_final, 4, '0', STR_PAD_LEFT);

            $this->db->table('ospos_customers')
                ->where('person_id', $customer_id_final)
                ->update(['account_number' => $account_code]);
        }

        // 4. إنشاء/تحديث الحساب المحاسبي
        $this->ensure_accounting_account($customer_id_final);

        return $customer_id_final;
    }

    // ============================
    // 🔥 إنشاء الحساب إذا غير موجود
    // ============================
    public function ensure_accounting_account($customer_id)
    {
        log_message('error', 'STEP 2: ensure_accounting_account called with ID = ' . $customer_id);
        $customer_data = $this->db->table('ospos_customers c')
        ->select('c.account_number, p.first_name, p.last_name')
        ->join('ospos_people p', 'p.person_id = c.person_id')
        ->where('c.person_id', $customer_id)
        ->get()
        ->getRowArray();

        $account_code = trim($customer_data['account_number']);

        if (empty($account_code)) {
            return;
        }
            // log_message('error', 'STEP 2: SERVICE CALLED', ['account_code' => $account_code]);

        // $account_code = trim($customer->account_number);

        $account = $this->accounting->get_account_by_code($account_code);

        $parent = $this->accounting->get_account_by_code('1200'); // العملاء

        
        $name = trim(
            ($customer_data['first_name'] ?? '') . ' ' .
            ($customer_data['last_name'] ?? '')
        );

        $data = [
        'account_code' => $account_code,
        'account_name' => $name,
        'account_type' => 'Asset',
        'parent_id'    => $parent->account_id ?? 3,
        ];
        // dd($account);
        log_message('error', 'STEP 2: SERVICE CALLED');
        log_message('error', json_encode($account));
        if ($account)
        {
            // 🔥 UPDATE بدل إنشاء
            $this->accounting->update_account($account->account_id, $data);
        }
        else
        {
            // 🔥 CREATE فقط إذا غير موجود
            
            $this->accounting->create_account($data);
        }
    }
    public function after_save($customer_id)
    {
        
        log_message('error', 'STEP 1: after_save called with ID = ' . $customer_id);
        $customer = $this->customer->get_info($customer_id);

        if (empty($customer)) return;

        // 🧠 1. توليد account_number إذا فارغ
        if (empty($customer->account_number))
        {
            $account_code = 'C' . str_pad($customer_id, 4, '0', STR_PAD_LEFT);

            $this->customer->update($customer_id, [
                'account_number' => $account_code
            ]);
        }
        else
        {
            $account_code = trim($customer->account_number);
        }

        // 🧠 2. إنشاء الحساب المحاسبي
        $this->create_or_update_account($customer, $account_code);
    }
    private function create_or_update_account($customer, $account_code)
{
    $parent = $this->accounting->get_account_by_code('1200');

    $account_name = !empty($customer->company_name)
        ? $customer->company_name
        : trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''));

    $data = [
        'account_code' => $account_code,
        'account_name' => $account_name,
        'account_type' => 'Asset',
        'parent_id'    => $parent->account_id ?? 3,
    ];

    $account = $this->accounting->get_account_by_code($account_code);

    if ($account)
    {
        $this->accounting->update_account($account->account_id, [
            'account_name' => $account_name
        ]);
    }
    else
    {
        $this->accounting->create_account($data);
    }
}
}