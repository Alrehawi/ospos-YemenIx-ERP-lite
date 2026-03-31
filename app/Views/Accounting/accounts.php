<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>



<br><br>
<div class="container">
    <div class="page-header">
        <h3><i class="fa fa-list"></i> دليل الحسابات</h3>
    </div>
    <a href="<?= site_url('accounting/account/create') ?>" class="btn btn-success">
    ➕ إضافة حساب
    </a>
    <table class="table table-striped table-bordered text-center">
        <thead style="background-color: red; color: white; text-align: center;">
            <tr>
                <th>الكود</th>
                <th>اسم الحساب</th>
                <th>النوع</th>
                <th>الرصيد</th>
                <th>إجراء</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($accounts as $acc): ?>
            <tr>
                <td><?= $acc['account_code'] ?></td>
                <td><?= $acc['account_name'] ?></td>
                <td><?= $acc['account_type'] ?></td>
                <td><?= (new \App\Models\Account())->get_balance($acc['account_id']) ?></td>
                <td>
                    <a href="<?= site_url('accounting/account/edit/'.$acc['account_id']) ?>" 
                    class="btn btn-xs btn-primary">
                        تعديل
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partial/footer') ?>