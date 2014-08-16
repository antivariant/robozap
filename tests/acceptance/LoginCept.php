<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('залогиниться и выйти c главной');
//Вход
$I->amOnPage('/');
$I->click('Войти');
$I->see('РЕГИСТРАЦИЯ И ВХОД');
$I->fillField(['name'=>'username'], Admin::$username);
$I->fillField(['name'=>'password'], Admin::$password);
$I->click('#login-form input[type=submit]');
$I->see('Выйти');
//Выход
$I->click('Выйти');
$I->click('#login-form input[type=submit]');
$I->see('Войти!');
