<?php

use yii\helpers\Html;
use humhub\modules\space\models\Space;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\FileHandlerButtonDropdown;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\file\widgets\FilePreview;

\humhub\modules\content\assets\ContentFormAsset::register($this);

$this->registerJsConfig('content.form', [
    'defaultVisibility' => $defaultVisibility,
    'disabled' => ($contentContainer instanceof Space && $contentContainer->isArchived()),
    'text' => [
        'makePrivate' => Yii::t('ContentModule.widgets_views_contentForm', 'Make private'),
        'makePublic' => Yii::t('ContentModule.widgets_views_contentForm', 'Make public'),
        'info.archived' => Yii::t('ContentModule.widgets_views_contentForm', 'This space is archived.')
]]);
?>

<div class="panel panel-default clearfix">
    <div class="panel-body" id="contentFormBody" style="display:none;" data-action-component="content.form.CreateForm" >
        <?= Html::beginForm($submitUrl, 'POST'); ?>

        <?= $form; ?>

        <div id="notifyUserContainer" class="form-group" style="margin-top: 15px;display:none;">
            <?= UserPickerField::widget([
                'id' => 'notifyUserInput',
                'url' => ($contentContainer instanceof Space) ? $contentContainer->createUrl('/space/membership/search') : null,
                'formName' => 'notifyUserInput',
                'maxSelection' => 10,
                'disabledItems' => [Yii::$app->user->guid],
                'placeholder' => Yii::t('ContentModule.widgets_views_contentForm', 'Add a member to notify'),
            ]);
            ?>
        </div>

        <?= Html::hiddenInput('containerGuid', $contentContainer->guid); ?>
        <?= Html::hiddenInput('containerClass', get_class($contentContainer)); ?>

        <ul id="contentFormError"></ul>

        <div class="contentForm_options">
            <hr>
            <div class="btn_container">
                <button id="post_submit_button" data-action-click="submit" data-action-submit data-ui-loader class="btn btn-info">
                    <?= $submitButtonText ?>
                </button>

                <?php
                $uploadButton = UploadButton::widget([
                            'id' => 'contentFormFiles',
                            'progress' => '#contentFormFiles_progress',
                            'preview' => '#contentFormFiles_preview',
                            'dropZone' => '#contentFormBody',
                            'max' => Yii::$app->getModule('content')->maxAttachedFiles
                ]);
                ?>
                <?= FileHandlerButtonDropdown::widget([
                            'primaryButton' => $uploadButton,
                            'handlers' => $fileHandlers,
                            'cssButtonClass' => 'btn-default'
                ]);
                ?>

                <!-- public checkbox -->
                <?= Html::checkbox('visibility', '', [
                            'id' => 'contentForm_visibility',
                            'class' => 'contentForm hidden',
                            'aria-hidden' => 'true',
                            'title' => Yii::t('ContentModule.widgets_views_contentForm', 'Content visibility')
                ]);
                ?>

                <!-- content sharing -->
                <div class="pull-right">

                    <span class="label label-info label-public hidden">
                        <?= Yii::t('ContentModule.widgets_views_contentForm', 'Public'); ?>
                    </span>

                    <?php if ($canSwitchVisibility) : ?>
                    <ul class="nav nav-pills preferences" style="right:0; top:5px;">
                        <li class="dropdown">
                            <a class="dropdown-toggle" style="padding:5px 10px;" data-toggle="dropdown" href="#"
                               aria-label="<?= Yii::t('base', 'Toggle post menu'); ?>" aria-haspopup="true">
                                <i class="fa fa-cogs"></i>
                            </a>

                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a id="contentForm_visibility_entry" data-action-click="changeVisibility">
                                        <i class="fa fa-unlock"></i> <?= Yii::t('ContentModule.widgets_views_contentForm', 'Make public'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <?= UploadProgress::widget(['id' => 'contentFormFiles_progress']) ?>
            <?= FilePreview::widget([
                'id' => 'contentFormFiles_preview',
                'edit' => true,
                'options' => ['style' => 'margin-top:10px']
            ]);
            ?>

        </div>
        <!-- /contentForm_Options -->
        <?= Html::endForm(); ?>
    </div>
    <!-- /panel body -->
</div> 
<!-- /panel -->
