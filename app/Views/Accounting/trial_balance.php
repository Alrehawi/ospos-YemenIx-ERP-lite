<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>

<div class="container">
    <div class="page-header">
        <h3><i class="fa fa-balance-scale"></i> ميزان المراجعة</h3>
    </div>

    <table class="table table-striped table-bordered">
        <thead style="background-color: red; color: white; text-align: center;">
            <tr>
                <th>الحساب</th>
                <th>مدين</th>
                <th>دائن</th>
                <th>الرصيد</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $total_debit = 0;
            $total_credit = 0;
            ?>

            <?php foreach($trial as $row): ?>
                <?php 
                    $total_debit += $row->total_debit ?? 0;
                    $total_credit += $row->total_credit ?? 0;
                ?>
                <tr>
                    <td><?= $row->account_name ?></td>
                    <td><?= $row->total_debit ?? 0 ?></td>
                    <td><?= $row->total_credit ?? 0 ?></td>
                    <td><?= $row->balance ?? 0 ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr style="font-weight:bold;">
                <td>الإجمالي</td>
                <td><?= $total_debit ?></td>
                <td><?= $total_credit ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<?= view('partial/footer') ?>