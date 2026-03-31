<?= view('partial/header') ?>

<div class="container">

    <h3><?= isset($account) ? 'تعديل حساب' : 'إضافة حساب' ?></h3>

    <form method="post" action="<?= site_url('accounting/account/save') ?>">

        <input type="hidden" name="account_id" value="<?= $account['account_id'] ?? '' ?>">

        <div class="form-group">
            <label>كود الحساب</label>
            <input type="text" name="account_code" class="form-control"
                   value="<?= $account['account_code'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>اسم الحساب</label>
            <input type="text" name="account_name" class="form-control"
                   value="<?= $account['account_name'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>نوع الحساب</label>
            <select name="account_type" class="form-control">
                <option value="Asset">Asset</option>
                <option value="Liability">Liability</option>
                <option value="Equity">Equity</option>
                <option value="Revenue">Revenue</option>
                <option value="Expense">Expense</option>
            </select>
        </div>

        <div class="form-group">
            <label>الوصف</label>
            <input type="text" name="description" class="form-control"
                   value="<?= $account['description'] ?? '' ?>">
        </div>

        <button class="btn btn-primary">💾 حفظ</button>

    </form>

</div>

<?= view('partial/footer') ?>