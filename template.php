<?php

/**
* @var string $timeOffset
* @var string $city
* @var string $phone
* @var string $email
 */
?>
<div class="footer__info">
    <div class="footer__time">
        <div class="footer__time_clock">
            <div class="clock" data-time="<?= $timeOffset ?>">
                <div class="hour"></div>
                <div class="minute"></div>
            </div>
        </div>
        <div class="footer__time_region">
            <?= $city ?>
        </div>
    </div>
    <div class="footer__contact">
        <div class="footer__contact_item">
            <p class="footer__contact_title">Телефон:</p>
            <a href="tel:<?= $phone ?>" class="footer__contact_info"><?= $phone ?></a>
        </div>
        <div class="footer__contact_item">
            <p class="footer__contact_title">E-mail:</p>
            <a href="mailto:<?= $email ?>" class="footer__contact_info"><?= $email ?></a>
        </div>
    </div>
</div>
