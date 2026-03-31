<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>

<div class="container">
    <div class="page-header">
        <h3><i class="fa fa-money"></i> الأرباح والخسائر</h3>
    </div>

    <table class="table table-bordered text-center">
        <tr>
            <th>الإيرادات</th>
            <td><?= $revenue->total ?? 0 ?></td>
        </tr>
        <tr>
            <th>المصروفات</th>
            <td><?= $expense->total ?? 0 ?></td>
        </tr>
        <tr style="font-weight:bold;">
            <th>صافي الربح</th>
            <td><?= ($revenue->total ?? 0) - ($expense->total ?? 0) ?></td>
        </tr>
    </table>
</div>

<?= view('partial/footer') ?>