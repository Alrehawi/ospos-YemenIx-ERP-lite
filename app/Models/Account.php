<?php

namespace App\Models;

use CodeIgniter\Model;

class Account extends Model
{
    protected $table = 'ospos_accounts';
    protected $primaryKey = 'account_id';

    protected $allowedFields = [
        'account_code',
        'account_name',
        'account_type',
        'parent_id',
        'description'
    ];

    protected $useTimestamps = false;

    /**
     * 📚 جلب كل الحسابات مرتبة
     */
    public function get_all_accounts()
    {
        return $this->orderBy('account_code', 'ASC')->findAll();
    }

    /**
     * 🔍 جلب حساب واحد
     */
    public function get_account($account_id)
    {
        return $this->where('account_id', $account_id)->first();
    }

    /**
     * 🌳 جلب الحسابات الرئيسية (بدون parent)
     */
    public function get_main_accounts()
    {
        return $this->where('parent_id', null)->findAll();
    }

    /**
     * 🌿 جلب الحسابات الفرعية
     */
    public function get_child_accounts($parent_id)
    {
        return $this->where('parent_id', $parent_id)->findAll();
    }

    /**
     * ➕ إنشاء حساب
     */
    public function create_account($data)
    {
        return $this->insert($data);
    }

    /**
     * ✏️ تحديث حساب
     */
    public function update_account($account_id, $data)
    {
        return $this->update($account_id, $data);
    }

    /**
     * ❌ حذف حساب
     */
    public function delete_account($account_id)
    {
        return $this->delete($account_id);
    }

    /**
     * 💰 حساب الرصيد
     */
    public function get_balance($account_id)
    {
        $db = \Config\Database::connect();

        $result = $db->query("
            SELECT 
                SUM(debit) as total_debit,
                SUM(credit) as total_credit
            FROM ospos_journal_items
            WHERE account_id = ?
        ", [$account_id])->getRow();

        $debit = $result->total_debit ?? 0;
        $credit = $result->total_credit ?? 0;

        return $debit - $credit;
    }

    /**
     * 📊 رصيد كل الحسابات
     */
    public function get_all_balances()
    {
        $db = \Config\Database::connect();

        return $db->query("
            SELECT 
                a.account_id,
                a.account_name,
                a.account_type,
                SUM(ji.debit) as total_debit,
                SUM(ji.credit) as total_credit,
                (SUM(ji.debit) - SUM(ji.credit)) as balance
            FROM ospos_accounts a
            LEFT JOIN ospos_journal_items ji 
                ON ji.account_id = a.account_id
            GROUP BY a.account_id
            ORDER BY a.account_code
        ")->getResult();
    }

    /**
     * 🧠 جلب الحساب حسب النوع
     */
    public function get_by_type($type)
    {
        return $this->where('account_type', $type)->findAll();
    }
}