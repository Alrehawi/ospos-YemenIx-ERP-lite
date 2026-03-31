<?php

namespace App\Libraries;

use App\Models\Accounting_model;
use App\Models\Item;
use App\Models\Item_kit;
use App\Models\Customer;

class AccountingService
{
    protected $accounting;
    protected $item_model;
    protected $item_kit_model;
    protected $customer_model;

    public function __construct()
    {
        $this->accounting = new Accounting_model();
        $this->item_model = new Item();
        $this->item_kit_model = new Item_kit();
        $this->customer_model = new Customer();
    }

    public function sale($sale_id, $total, $customer_id, $payments, $cart)
    {
        // 🔥 الحسابات
        $cash_account       = $this->accounting->get_account('cash_account');
        $bank_account       = $this->accounting->get_account('bank_account');
        $sales_account      = $this->accounting->get_account('sales_account');
        $receivable_account = $this->accounting->get_account('receivable_account');

        // 🔥 حساب العميل (Child)
        $customer_account_id = null;

        if (!empty($customer_id))
        {
            $customer = $this->customer_model->get_info($customer_id);

            if (!empty($customer->account_number))
            {
                $acc = $this->accounting->get_account_by_code($customer->account_number);
                $customer_account_id = $acc->account_id ?? null;
            }
        }

        $items = [];

        // =========================
        // 🟢 Multi Payment FIX
        // =========================
        foreach ($payments as $payment)
        {
            $type   = strtolower($payment['payment_type']);
            $amount = $payment['payment_amount'];

            if ($amount <= 0) continue;

            log_message('error', 'TYPE: ' . $type . ' AMOUNT: ' . $amount);

            // 💰 Cash
            if (stripos($type, 'cash') !== false)
            {
                $items[] = [
                    'account_id' => $cash_account,
                    'debit'      => $amount,
                    'credit'     => 0
                ];
            }
            // 💳 Bank / Card
            elseif (stripos($type, 'credit') !== false || stripos($type, 'card') !== false)
            {
                $items[] = [
                    'account_id' => $bank_account,
                    'debit'      => $amount,
                    'credit'     => 0
                ];
            }
            // 🔥 Due → حساب العميل
            elseif (stripos($type, 'due') !== false || stripos($type, 'مستحق') !== false)
            {
                $items[] = [
                    'account_id' => $customer_account_id ?? $receivable_account,
                    'debit'      => $amount,
                    'credit'     => 0
                ];
            }
            // fallback
            else
            {
                $items[] = [
                    'account_id' => $cash_account,
                    'debit'      => $amount,
                    'credit'     => 0
                ];
            }
        }

        // =========================
        // 🔴 Sales
        // =========================
        $items[] = [
            'account_id' => $sales_account,
            'debit'      => 0,
            'credit'     => $total
        ];

        // =========================
        // 💣 COGS
        // =========================
        $total_cost = 0;

        foreach ($cart as $item)
        {
            $qty = $item['quantity'];

            if (!empty($item['item_id']) && empty($item['item_kit_id']))
            {
                $item_info = $this->item_model->get_info($item['item_id']);
                $total_cost += ($item_info->cost_price ?? 0) * $qty;
            }
            elseif (!empty($item['item_kit_id']))
            {
                $kit_cost = $this->item_kit_model->get_kit_cost($item['item_kit_id']);
                $total_cost += $kit_cost * $qty;
            }
        }

        if ($total_cost > 0)
        {
            $inventory_account = $this->accounting->get_account('inventory_account');
            $cogs_account      = $this->accounting->get_account('cogs_account');

            $items[] = [
                'account_id' => $cogs_account,
                'debit'      => $total_cost,
                'credit'     => 0
            ];

            $items[] = [
                'account_id' => $inventory_account,
                'debit'      => 0,
                'credit'     => $total_cost
            ];
        }

        // =========================
        // 🧾 Entry
        // =========================
        $entry = [
            'entry_date'  => date('Y-m-d'),
            'reference'   => 'SALE-' . $sale_id,
            'description' => 'فاتورة بيع رقم ' . $sale_id,
            'created_by'  => session()->get('person_id') ?? 1
        ];

        return $this->accounting->create_entry(
            $entry,
            $items,
            [
                'table' => 'ospos_sales',
                'id'    => $sale_id
            ]
        );
    }
}