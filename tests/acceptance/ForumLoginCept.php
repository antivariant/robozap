<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('залогиниться в форум на странце форума');
$I->amOnPage('/');
$I->click('Форум');
$I->fillField(['name'=>'username'],'antivariant');
$I->fillField(['name'=>'password'],'masterkey');
$I->click('Войти');
$I->see('Добро пожаловать');
