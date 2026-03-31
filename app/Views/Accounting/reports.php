<?= view('partial/header') ?>
<?= view('accounting/partials/menu') ?>

<div class="container">
    <div class="page-header">
        <h3><i class="fa fa-bar-chart"></i> التقارير</h3>
    </div>

    <div class="row text-center rtl-row">

        <div class="col-md-4">
            <a href="<?= site_url('accounting/ledger') ?>" class="btn btn-info btn-lg btn-block">
                <i class="fa fa-table"></i><br><br>
                الاستاذ العام
            </a>
        </div>

        <div class="col-md-4">
            <a href="<?= site_url('accounting/trial-balance') ?>" class="btn btn-warning btn-lg btn-block">
                <i class="fa fa-balance-scale"></i><br><br>
                ميزان المراجعة
            </a>
        </div>

        <div class="col-md-4">
            <a href="<?= site_url('accounting/profit-loss') ?>" class="btn btn-success btn-lg btn-block">
                <i class="fa fa-money"></i><br><br>
                الارباح والخسائر
            </a>
        </div>

    </div>
</div>

<?= view('partial/footer') ?>