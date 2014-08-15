<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('залогиниться в форум через форум');
$I->amOnPage('/');
$I->click('Форум');
$I->fillField(['name'=>'username'],Admin::$username);
$I->fillField(['name'=>'password'],Admin::$password);
$I->click('Войти');
$I->see('Добро пожаловать');
