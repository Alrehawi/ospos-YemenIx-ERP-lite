<?php

namespace App\Models;

use CodeIgniter\Model;

class Accounting_model extends Model
{
    public function create_entry($entry, $items, $link = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. journal_entries
        $db->table('ospos_journal_entries')->insert($entry);
        $journal_id = $db->insertID();

        // 2. journal_items
        foreach ($items as $item)
        {
            $item['journal_id'] = $journal_id;
            $db->table('ospos_journal_items')->insert($item);
        }

        // 3. accounting_links
        if($link)
        {
            $db->table('ospos_accounting_links')->insert([
                'journal_id' => $journal_id,
                'source_table' => $link['table'],
                'source_id' => $link['id']
            ]);
        }

        $db->transComplete();

        return $journal_id;
    }
    public function get_account($key)
        {
            $row = $this->db->table('ospos_account_settings')
                ->where('key_name', $key)
                ->get()
                ->getRowArray();

            return $row ? $row['account_id'] : null;
        }
public function get_account_by_code($code)
{
    // log_message('error', 'STEP 2: SERVICE CALLED',  $code);
      return $this->db->table($this->db->prefixTable('accounts'))
        ->where('LOWER(TRIM(account_code))', strtolower(trim($code)))
        ->get()
        ->getRow();
}

public function create_account($data)
{
    return $this->db->table($this->db->prefixTable('accounts'))
        ->insert($data);
}
public function update_account($account_id, $data)
{
    return $this->db->table($this->db->prefixTable('accounts'))
        ->where('account_id', $account_id)
        ->update($data);
}

}