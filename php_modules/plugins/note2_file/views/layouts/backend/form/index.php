
<?php echo $this->renderWidget('core::notification'); ?>
<div class="container-fluid align-items-center row justify-content-center mx-auto pt-3">
    <form enctype="multipart/form-data" action="<?php echo $this->link_form . '/' . $this->id ?>" method="post" id="form_submit">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-sm-12">
                <input id="input_title" type="hidden" name="title" required>
                <input id="_method" type="hidden" name="_method" value="<?php echo $this->id ? 'PUT' : 'POST' ?>">
                <div>
                    <?php if (!$this->data) : ?>
                    <?php $this->ui->field('file'); ?>
                    <?php else : ?>
                    <a href="<?php echo $this->url($this->data['path']);?>"><?php echo basename($this->data['path'])?></a>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <?php echo $this->renderWidget('tag::backend.tags'); ?>
                </div>
                <div class="mt-3">
                    <?php $this->ui->field('notice'); ?>
                </div>
                <input id="save_close" type="hidden" name="save_close">
            </div>
        </div>
    </form>
</div>
<?php echo $this->render('backend.form.javascript'); ?>

