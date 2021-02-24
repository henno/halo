<script src="https://use.fontawesome.com/d37013578f.js"></script>
<style>
    .search {
        width: 100%
    }

    #language-table td:last-child, #language-table th:last-child {
        width: 20px;
        vertical-align: middle;
    }

    #language-table tr:last-child td:first-child {
        vertical-align: middle;
        padding: 0 8px 0 0;
    }

    #language-name, #language-name.form-control:focus, {
        border: 0 !important;
        height: 48px !important;
        box-shadow: none;

    }

    #query:focus {
        box-shadow: none;
    }

    #add-language-modal > div > div > div.modal-header > button {
        color: white;
    }


    #add-language-modal .modal-body {
        padding: 0;
    }

    #language-table {
        margin-bottom: 0
    }

    .fa:hover {
        color: #007bff;
        cursor: pointer;
    }

    #language-table > tbody > tr > td:nth-child(2) > i {
        color: red
    }

    #language-table > tbody > tr:last-child td:nth-child(2) > i {
        color: mediumseagreen;
    }

    #add-language-modal .modal-header {
        background-color: #212529 !important;
        color: white;
        border: 1px solid #212529;
        border-radius: 0;
    }

    table {
        text-align: left;
        position: relative;
        background-color: white;
    }

    tr:first-child th {
        position: sticky;
        top: 72px;
    }

    tr:nth-child(2) th {
        color: red !important;
        position: sticky;
        top: 132px;
    }

    tr.help-block th {
        font-weight: normal;
        padding: 6px 0 0 9px;
        color: gray;
    }

    #translations-table > thead > tr:nth-child(1) > th > label {
        font-weight: normal;
        color: lightgray;
        white-space: nowrap;
    }

    #translations-table > thead > tr:nth-child(1) > th {
        vertical-align: top;
    }

    #th-search {
        padding: 4px;
    }

    #translations-table {
        width: 100% !important;
    }

    i.fa:hover {
        color: white
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 24px;
        height: 24px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<?php if (!empty($translations_where_phrase_is_too_long)): ?>
    <?php foreach ($translations_where_phrase_is_too_long as $item): ?>
        <div class="alert alert-danger">You have a translation phrase <b><?= $item['translationPhrase'] ?></b> which is
            too long to store effectively in database. To avoid errors, phrase must be shortened in the code.
        </div>
    <?php endforeach ?>
<?php endif; ?>

<table id="translations-table" class="table table table-nonfluid table-bordered table-hover table-users bordered">
    <thead>
    <tr>
        <th>#</th>
        <th><?= __('Phrase') ?></th>
        <?php foreach ($languagesInUse as $languageCode => $languageName): ?>
            <th><?= $languageName ?><br>
                <label>
                    <input type="checkbox"
                           class="show-untranslated"
                           data-lang="<?= $languageCode ?>"
                        <?= isset($showUntranslated[$languageCode]) ? 'checked' : '' ?>> <?= __('Untranslated') ?>
                </label>
            </th>
        <?php endforeach ?>
    </tr>
    <tr>
        <th id="th-search" colspan="<?= count($languagesInUse) + 2 ?>">

            <div class="input-group">
                <input type="text" id="query" class="form-control search" placeholder="<?= __('Search') ?>">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="button" data-toggle="modal" role="button"
                            data-target="#add-language-modal">
                        <i class="fa fa-language" aria-hidden="true"></i> <?= __('Languages') ?>
                    </button>
                </div>
            </div>


        </th>
    </tr>
    </thead>
    <tbody class="lookup">
    <?php foreach ($translations as $t): ?>
        <tr data-id="<?= $t['translationId'] ?>">
            <td><?= ++$n ?></td>
            <td style="white-space: pre-line"><?= $t['translationPhrase'] ?></td>
            <?php foreach ($languagesInUse as $languageCode => $languageName): ?>
                <td class="editable"
                    data-lang="<?= $languageCode ?>"><?= $t['translationIn' . ucfirst($languageCode)] ?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="add-language-modal" tabindex="-1" role="dialog" aria-labelledby="add-language-modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Languages</h5>
                <button type="button" class="close btn-close-language-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="language-table">
                                <tbody>
                                <?php foreach ($languagesInUse as $languageCode => $languageName): ?>
                                    <tr>
                                        <td data-lang="<?= $languageCode ?>"><?= $languageName ?>
                                            <?php if ($statistics[$languageCode]['translated'] != $statistics['total']): ?>
                                                <sup><span class="badge badge-danger">
                                                    <?= $statistics[$languageCode]['remaining'] ?>
                                                </span></sup>

                                                <span style="float:right;">
                                                    <a class="btn btn-primary btn-translate-remaining"
                                                       style="padding: 2px 6px 4px 4px"
                                                       href="javascript:undefined" role="button"
                                                       data-lang="<?= $languageCode ?>">
                                                            <img src="assets/img/gtranslate.ico" alt="Google Translate"
                                                                 style="width:20px">
                                                        Google Translate
                                                    </a>
                                                </span>

                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <i class="fa fa-minus-square fa-lg delete-language-link"
                                               aria-hidden="true"></i>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="add-language-td">
                                        <select class="form-control" name="language-name" id="language-name">
                                            <option value="">-- <?= __('Select language') ?> --</option>
                                            <?php foreach ($languagesNotInUse as $languageCode => $languageName): ?>
                                                <option value="<?= $languageCode ?>"><?= $languageName ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                    <td class="add-language-td">
                                        <i id="add-language-link" class="fa fa-plus-square fa-lg"
                                           aria-hidden="true"></i>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="help-block"
                      style="float: left"><sup>*</sup> <?= __('Google translates < 5000 chars at a time') ?></span>
                <button style="float: right" type="button" class="btn btn-secondary btn-close-language-modal"
                        data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>
<script src="views/admin/admin_translations.js?<?= COMMIT_HASH ?>"></script>
<script>

    $('#add-language-link').on('click', function () {
        let clickedBtn = $(this)
        let languageCode = $(this).parents('tr').find('td:nth-child(1) select').val()

        if(!languageCode){
            alert('<?=__('Select language first')?>')
            return
        }

        // Create a spinner and insert it after the button
        let spinner = $('<div class="loader"></div>')

        spinner.insertAfter(clickedBtn);
        clickedBtn.css('display','none');

        ajax('admin/translationAddLanguage', {
            languageCode
        },RELOAD, function(res){
            spinner.remove();
            clickedBtn.css('display','block');
            show_error_modal(res);
        })
    })

    // Reload page when language manager modal is closed
    $('.btn-close-language-modal').on('click', function () {
        location.reload();
    });

    $('.delete-language-link').on('click', function () {

        let clickedBtn = $(this)
        let languageCode = clickedBtn.parents('tr').find('td:nth-child(1)').data('lang')

        if (!confirm('<?=__('Are you really sure you want to remove the language %%% and destroy its translations?')?>'.replace('%%%', languageCode.toUpperCase()))) {
            return false;
        }

        // Create a spinner and insert it after the button
        let spinner = $('<div class="loader"></div>')
        spinner.insertAfter(clickedBtn);

        ajax('admin/translationDeleteLanguage', {
            languageCode
        }, function(){
            clickedBtn.parents('tr').remove();
        } )

        // Hide the Google Translate button
        clickedBtn.css('display', 'none');
    })

    $('.btn-translate-remaining').on('click', function () {

        let clickedBtn = $(this)
        let languageCode = clickedBtn.data('lang')

        // Hide the Google Translate button
        clickedBtn.css('display', 'none');

        // Create a spinner and insert it after the button
        let spinner = $('<div class="loader"></div>')
        spinner.insertAfter(clickedBtn);

        ajax('admin/translateRemainingStrings', {
            languageCode
        }, function (res) {

            // Remove the spinner
            spinner.remove();

            // If all strings are translated
            if (res.data.untranslatedCount === "0") {


                // Remove the red badge
                clickedBtn.parents('td').find('.badge-danger').remove();

                // Remove Google Translate button
                clickedBtn.remove();

            } else {

                // Set the badge to untranslated strings count
                clickedBtn.parents('td').find('.badge-danger').html(res.data.untranslatedCount)


                // Re-show the Google Translate button
                clickedBtn.prop('disabled', false);
                clickedBtn.css('display', 'block');
            }
        })
    })

    $('input[type="checkbox"]').on('change', function () {

        let filteredLanguages = [];

        $('.show-untranslated:checked').each(function (index, element) {
            console.log(element)
            filteredLanguages.push($(element).data('lang'))
        })

        let filter = filteredLanguages.join();

        location.href = `admin/translations${filter ? '?showUntranslated=' + filter : ''}`
    });

    // Widen the table to full page width
    $('.container').addClass('container-fluid').removeClass('container');

    let resetTranslationTableHeaderPosition = function () {

        // Fix mobile view by setting minimum width of the page to be the sufficient for all language columns
        $('body').css('min-width', +<?=150 + (130 * count($languagesInUse))?>);

        $('tr:first-child th').css('top', $('nav').height() + 16)
    }
    resetTranslationTableHeaderPosition();
    window.onresize = resetTranslationTableHeaderPosition;

</script>