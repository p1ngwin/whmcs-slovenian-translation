## Slovenski prevod WHMCS-ja / Slovenian translation of WHMCS



### Kako uporabiti / How to use?

Prenesite si datoteko lang/slovenian.php in jo namestite v vaš whmcs/lang/ direktorij.
To je vse.

Download lang/slovenian.php and put it in your whmcs/lang/ directory.
That is it.



### Orodje za pomoč prevajanju / translation assistance tool?

To orodje poskrbi, da ciljna jezikovna datoteka vsebuje vse prevode V ISTEM ZAPOREDJU kot izvorna jezikovna datoteka.
Prenese vse, tako prevode kot tudi vse komentarje in presledke. Če ciljna jezikovna datoteka že obstaja, uporabi
prevode, ki jih najde v njen, izpiše pa tiste, ki so odveč.

Uporaba:
```shell
git clone .../whmcs-translation-slovenian

cd whmcs-translation-slovenian

./sbin/init-repository

# Po 'defaultu' sinhronizira slovenski jezik na angleškega
./bin/langtool sync

# Lahko se uporabi tudi za druge jezike
./bin/langtool sync slovenian
./bin/langtool sync --source=english slovenian
```



### Kako prispevati popravke/nove prevode?

Klasičen GitHub workflow:
* fork
* create new branch
* hack-hack-hack
* commit (with a nice commit message)
* push to GitHub, to that new branch
* issue a pull request on GitHub

Opozorilo: s prispevanjem svojih popravkov v ta repozitorij se strinjate,
da so ti popravki na voljo upravljalcu tega repozitorija (in svetu) pod
MIT licenco.



### Lokacije git repozitorijev:

Primarni repozitorij za javno sprejemanje popravkov:
* https://github.com/SpletnaSoba/whmcs-slovenian-translation

Backup mirrorji:
* https://bitbucket.org/SpletnaSoba/whmcs-slovenian-translation
* https://gitlab.com/spletnasoba/whmcs-slovenian-translation
* https://git.teon.si/spletnasoba/whmcs-slovenian-translation (interni primarni repozitorij)
