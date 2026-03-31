<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>

<div class="container">

    <div class="page-header">
        <h3><i class="fa fa-table"></i> دفتر الأستاذ</h3>
    </div>

    <!-- 🔍 الفلترة -->
    <form method="get" class="form-inline">

        <input type="date" name="from" class="form-control" value="<?= $from ?>">
        <input type="date" name="to" class="form-control" value="<?= $to ?>">

        <select name="account_id" class="form-control">
            <option value="">اختر حساب</option>
            <?php foreach($accounts as $acc): ?>
                <option value="<?= $acc['account_id'] ?>" 
                    <?= ($selected_account == $acc['account_id']) ? 'selected' : '' ?>>
                    <?= $acc['account_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-primary">عرض</button>

    </form>

    <br>

    <?php if(!empty($ledger)): ?>

    <table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th>التاريخ</th>
                <th>البيان</th>
                <th>مدين</th>
                <th>دائن</th>
                <th>الرصيد</th>
            </tr>
        </thead>

        <tbody>
            
            <?php $balance = 0; ?>

            <!-- 🔥 رصيد افتتاحي -->
            <tr style="background:#eee; font-weight:bold;">
                <td colspan="4">رصيد افتتاحي</td>
                <td><?= $balance ?></td>
            </tr>

            <?php foreach($ledger as $row): ?>

                <?php $balance += $row->debit - $row->credit; ?>

                <tr>
                    <td><?= $row->entry_date ?></td>
                    <td><?= $row->description ?></td>
                    <td><?= $row->debit ?></td>
                    <td><?= $row->credit ?></td>

                    <td style="color: <?= $balance >= 0 ? 'green' : 'red' ?>">
                        <?= $balance ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <?php endif; ?>

</div>

<?= view('partial/footer') ?>