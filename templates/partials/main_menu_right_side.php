<?php use App\Translation;?>

<style>
    .nav-item{
        margin-right: 10px;
    }
</style>
<ul class="navbar-nav my-2 my-lg-0">
    <?php if(count($supported_languages) > 1): ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="javascript:undefined" id="main-menu-language-dropdown" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false"><?= $_SESSION['language'] ?></a>
        <div class="dropdown-menu" aria-labelledby="main-menu-language-dropdown">
            <?php foreach ($supported_languages as $languageCode): ?>
                <a class="dropdown-item" href="?language=<?= $languageCode ?>"><?= $languageCode ?></a>
            <?php endforeach ?>
        </div>
    </li>
    <?php endif ?>
    <li class="nav-item">
        <a class="nav-link" href="logout"><?= __('Logout') ?></a>
    </li>
</ul>