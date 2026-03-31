<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>

<div class="container">
    <div class="page-header">
        <h3><i class="fa fa-book"></i> القيود اليومية</h3>
    </div>

    <table class="table table-striped table-bordered text-center">
        <thead style="background-color: red; color: white; text-align: center;">
            <tr>
                <th>ID</th>
                <th>التاريخ</th>
                <th>الوصف</th>
                <th>تفاصيل</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($journals as $j): ?>
            <tr>
                <td><?= $j->journal_id ?></td>
                <td><?= $j->entry_date ?></td>
                <td><?= $j->description ?></td>
                <td>
                    <a href="<?= site_url('accounting/journal/'.$j->journal_id) ?>" class="btn btn-xs btn-info">
                        عرض
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= view('partial/footer') ?>