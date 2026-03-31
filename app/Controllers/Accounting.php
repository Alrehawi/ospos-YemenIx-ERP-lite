<?php

namespace App\Controllers;

use App\Models\Account;
use App\Models\Ledger_model;
use App\Models\Accounting_model;

class Accounting extends Secure_Controller
{
    private Account $account;
    private Ledger_model $ledger_model;
    private Accounting_model $accounting_model;

    public function __construct()
    {
        parent::__construct('accounting'); // 🔥 مهم للصلاحيات

        $this->account = model(Account::class);
        $this->ledger_model = new Ledger_model();
        $this->accounting_model = new Accounting_model();
    }

    /**
     * 📊 Dashboard
     */
    public function index()
    {
        return view('accounting/dashboard');
    }

    /**
     * 📚 Chart of Accounts
     */
    public function accounts()
    {
        $data['accounts'] = $this->account->findAll();

        return view('accounting/accounts', $data);
    }

    /**
     * ➕ إضافة حساب
     */
    public function storeAccount()
    {
        $this->account->save([
            'account_code' => $this->request->getPost('account_code'),
            'account_name' => $this->request->getPost('account_name'),
            'account_type' => $this->request->getPost('account_type'),
            'parent_id'    => $this->request->getPost('parent_id'),
            'description'  => $this->request->getPost('description'),
        ]);

        return redirect()->to('accounting/accounts');
    }

    /**
     * 📖 Journal Entries
     */
    public function journal()
    {
        $db = \Config\Database::connect();

        $data['journals'] = $db->query("
            SELECT * FROM ospos_journal_entries
            ORDER BY journal_id DESC
        ")->getResult();

        return view('accounting/journal', $data);
    }

    /**
     * 📄 عرض تفاصيل قيد
     */
    public function journalDetails($journal_id)
    {
        $db = \Config\Database::connect();

        $data['items'] = $db->query("
            SELECT a.account_name, ji.debit, ji.credit
            FROM ospos_journal_items ji
            JOIN ospos_accounts a ON a.account_id = ji.account_id
            WHERE ji.journal_id = ?
        ", [$journal_id])->getResult();

        return view('accounting/journal_details', $data);
    }

    /**
     * 📘 Ledger (دفتر الأستاذ)
     */
    public function ledger()
{
    $account_id = $this->request->getGet('account_id');
    $from = $this->request->getGet('from');
    $to = $this->request->getGet('to');

    $data['accounts'] = $this->account->findAll();
    $data['ledger'] = [];
    $data['selected_account'] = $account_id;
    $data['from'] = $from;
    $data['to'] = $to;

    if (!empty($account_id))
    {
        $data['ledger'] = $this->ledger_model->get_ledger($account_id, $from, $to);
    }

    return view('accounting/ledger', $data);
}

    /**
     * 📊 Reports
     */
    public function reports()
    {
        return view('accounting/reports');
    }

    /**
     * ⚖ Trial Balance
     */
    public function trialBalance()
    {
        $db = \Config\Database::connect();

        $data['trial'] = $db->query("
            SELECT 
                a.account_name,
                SUM(ji.debit) as total_debit,
                SUM(ji.credit) as total_credit,
                (SUM(ji.debit) - SUM(ji.credit)) as balance
            FROM ospos_accounts a
            LEFT JOIN ospos_journal_items ji ON ji.account_id = a.account_id
            GROUP BY a.account_id
        ")->getResult();

        return view('accounting/trial_balance', $data);
    }

    /**
     * 💰 Profit & Loss
     */
    public function profitLoss()
    {
        $db = \Config\Database::connect();

        $data['revenue'] = $db->query("
            SELECT SUM(ji.credit - ji.debit) as total
            FROM ospos_journal_items ji
            JOIN ospos_accounts a ON a.account_id = ji.account_id
            WHERE a.account_type = 'Revenue'
        ")->getRow();

        $data['expense'] = $db->query("
            SELECT SUM(ji.debit - ji.credit) as total
            FROM ospos_journal_items ji
            JOIN ospos_accounts a ON a.account_id = ji.account_id
            WHERE a.account_type = 'Expense'
        ")->getRow();

        return view('accounting/profit_loss', $data);
    }

            public function createAccount()
        {
            return view('accounting/account_form');
        }

        public function editAccount($id)
        {
            $data['account'] = $this->account->find($id);
            return view('accounting/account_form', $data);
        }

        public function saveAccount()
        {
            $this->account->save([
                'account_id'   => $this->request->getPost('account_id'),
                'account_code' => $this->request->getPost('account_code'),
                'account_name' => $this->request->getPost('account_name'),
                'account_type' => $this->request->getPost('account_type'),
                'parent_id'    => $this->request->getPost('parent_id'),
                'description'  => $this->request->getPost('description'),
            ]);

            return redirect()->to('accounting/accounts');
        }
}