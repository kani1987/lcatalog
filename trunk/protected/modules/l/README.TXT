@desription Catalog Editor based on yii to display the catalog on your website
@author NIBS http://nibs.ru

== depedencies ==
needs user module enabled with interface:

Yii::app() -> user
    -> isGuest
    -> loginRequired()
    -> isAdmin()
    
