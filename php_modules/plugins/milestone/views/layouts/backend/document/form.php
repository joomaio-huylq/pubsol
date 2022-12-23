<?php echo $this->render('notification'); ?>
<div class="container-fluid align-items-center row justify-content-center mx-auto pt-3 border-bottom border-3 border-dark" id="document_link">
    <div class="card shadow-none p-0 col-lg-12">
        <div class="card-body">
            <h2 class=" pb-4 border-bottom"><i class="fa-regular fa-folder-open pe-2"></i><?php echo $this->title_page_document ?></h2>
            <div class="row pt-3">
                <div class="col-lg-7 col-6 border-end">
                    <h4>Document:</h4>
                    <?php if ($this->editor) : ?>
                        <form id="form_document" action="<?php echo $this->link_form ?>" method="post">
                            <div class="row">
                                <div class="mb-3 col-sm-12 mx-auto">
                                    <?php $this->field('description'); ?>
                                </div>
                            </div>
                            <div class="row g-3 align-items-center m-0">
                                <?php $this->field('token'); ?>
                                <div class="col-xl-12 col-sm-12 text-center">
                                    <button type="submit" class="btn btn-outline-success">Apply</button>
                                </div>
                            </div>
                        </form>
                    <?php else :
                        echo ($this->data['description']);
                    ?>
                        <a href="<?php echo $this->link_form . '?editor=1' ?>" type="submit" class="btn btn-outline-success">Edit</a>
                    <?php
                    endif;
                    ?>
                </div>
                <div class="col-lg-5 col-6">
                    <h4>Discussion:</h4>
                    <ul id="list-discussion" class="list-unstyled pt-2" style="max-height: 60vh; overflow:auto;">
                    </ul>
                    <form id="form_comment" action="<?php echo $this->link_form_comment ?>" method="post">
                        <?php $this->field('token'); ?>
                        <div class="form-outline">
                            <textarea required name="message" class="form-control" id="textAreaExample2" rows="4"></textarea>
                            <div class="form-notch">
                                <div class="form-notch-leading" style="width: 9px;"></div>
                                <div class="form-notch-middle" style="width: 60px;"></div>
                                <div class="form-notch-trailing"></div>
                            </div>
                        </div>
                        <button type="submit" class="mt-2 btn btn-info btn-rounded float-end">Comment</button>
                    </form>
                </div>
                <div class="col-12">
                    <hr class="bg-danger border-2 border-top border-danger">
                    <h4>History:</h4>
                    <ul class="list-group list-group-flush" id="document_history">
                    </ul>
                </div>
            </div>


        </div>
    </div>
</div>
<script>
    function loadHistory(data)
    {
        $.ajax({
            url: '<?php echo $this->url. 'get-history/'. $this->request_id ?>',
            type: 'POST',
            data: data,
            success: function(resultData)
            {
                var list = '';
                if (Array.isArray(resultData))
                {
                    
                    resultData.forEach(function(item)
                    {
                        list += `
                        <li class="list-group-item">Edited at ${item['modified_at']}</li>
                        `
                    });
                    $("#document_history").html(list);
                }
            }
        })
    }
    loadHistory();
    function loadDiscussion(data)
    {
        const user_id = '<?php echo $this->user_id;?>'
        $.ajax({
            url: '<?php echo $this->url. 'get-comment/'. $this->request_id ?>',
            type: 'POST',
            data: data,
            success: function(resultData)
            {
                var list = '';
                if (Array.isArray(resultData))
                {
                    resultData.forEach(function(item)
                    {
                        if (user_id == item['user_id'])
                        {
                            var class_name = 'ms-5 me-2 justify-content-end';
                            var name = 'You';
                        }
                        else
                        {
                            var name = item['user'];
                            var class_name = 'me-5 ms-2 justify-content-between';
                        }
                        
                        list += `
                        <li class="d-flex ${class_name} mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between p-3">
                                    <p class="fw-bold mb-0">${name}</p>
                                    <p class="ms-2 text-muted small mb-0 align-self-center"><i class="far fa-clock"></i>${item['sent_at']}</p>
                                </div>
                                <div class="card-body pt-0">
                                    <p class="mb-0">
                                        ${item['message']}
                                    </p>
                                </div>
                            </div>
                        </li>
                        `
                    });
                    $("#list-discussion").html(list);
                    $("#list-discussion").scrollTop($("#list-discussion")[0].scrollHeight);
                }
            }
        })
    }
    loadDiscussion();
    $(document).ready(function() {
        $("#list-discussion").scrollTop($("#list-discussion")[0].scrollHeight);
        $("#description").attr('rows', 25);
        $("#form_document").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $("#form_document").attr('action'),
                data: $('#form_document').serialize(),
                success: function (result) {
                    if (result.result == 'ok')
                    {
                        $('#description').val('');
                    }
                    showMessage(result.result, result.message);
                    loadHistory();
                }
            });
        });

        $("#form_comment").on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $("#form_comment").attr('action'),
                data: $('#form_comment').serialize(),
                success: function (result) {
                    showMessage(result.result, result.message);
                    $('textarea[name=message]').val('');
                    loadDiscussion();
                }
            });
        });
    });
    
</script>