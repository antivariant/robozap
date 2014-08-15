<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('залогиниться и выйти');
//Вход
$I->amOnPage('/');
$I->click('Войти');
$I->see('РЕГИСТРАЦИЯ И ВХОД');
$I->fillField(['name'=>'username'],'antivariant');
$I->fillField(['name'=>'password'],'trustno1no2');
$I->click('#login-form input[type=submit]');
$I->see('Выйти');
//Выход
$I->click('Выйти');
$I->click('#login-form input[type=submit]');
$I->see('Войти');
