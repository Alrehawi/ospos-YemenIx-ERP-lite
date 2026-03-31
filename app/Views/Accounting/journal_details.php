<?= view('partial/header') ?>

<div class="container" dir="rtl">
    <div class="page-header">
        <h2>📄 تفاصيل القيد المحاسبي</h2>
    </div>

    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>الحساب</th>
                <th>مدين</th>
                <th>دائن</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_debit = 0;
            $total_credit = 0;
            ?>

            <?php foreach ($items as $row): ?>
                <tr>
                    <td><?= $row->account_name ?></td>
                    <td><?= number_format($row->debit, 2) ?></td>
                    <td><?= number_format($row->credit, 2) ?></td>
                </tr>

                <?php 
                $total_debit += $row->debit;
                $total_credit += $row->credit;
                ?>
            <?php endforeach; ?>

            <tr style="font-weight:bold; background:#f2f2f2;">
                <td>الإجمالي</td>
                <td><?= number_format($total_debit, 2) ?></td>
                <td><?= number_format($total_credit, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <a href="<?= site_url('accounting/journal') ?>" class="btn btn-default">
        رجوع
    </a>
</div>

<?= view('partial/footer') ?>