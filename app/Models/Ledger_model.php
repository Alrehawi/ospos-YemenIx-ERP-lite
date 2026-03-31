<?php

namespace App\Models;

use CodeIgniter\Model;

class Ledger_model extends Model
{
    public function get_ledger($account_id, $from = null, $to = null)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                j.entry_date,
                j.description,
                ji.debit,
                ji.credit
            FROM ospos_journal_items ji
            JOIN ospos_journal_entries j ON j.journal_id = ji.journal_id
            WHERE ji.account_id = ?
        ";

        $params = [$account_id];

        if (!empty($from)) {
            $sql .= " AND j.entry_date >= ?";
            $params[] = $from;
        }

        if (!empty($to)) {
            $sql .= " AND j.entry_date <= ?";
            $params[] = $to;
        }

        $sql .= " ORDER BY j.entry_date ASC";

        return $db->query($sql, $params)->getResult();
    }
}