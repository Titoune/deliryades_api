<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") . $user_city->user->fullname ?>,<br>
    Merci pour votre demande d'adhésion. Malheureusement nous avons le regret de vous annoncer que votre demande de
    suivi a été refusée. L'accès au compte MairesetCitoyens de votre commune est soumis à une validation de la part des
    équipes municipales. Il existe plusieurs motifs de refus possibles : <br>
    - Vous n'êtes pas inscrit avec votre vrai nom <br>
    - Vous n'êtes pas un administré de la commune <br>
    - Votre nom n'a pas été trouvé sur les listes électorales <br>
    - Vous n'avez pas répondu à une demande de précision concernant votre identité <br>
    - Vous avez déjà un autre compte sur la commune <br>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    Si vous pensez que votre compte aurait dû être validé car vous correspondez à tous les critères, vous pouvez
    répondre à ce mail en nous précisant votre demande, nous la transmettrons à
    votre <?= $user_city->city->account_user_type ?>.
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: left;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Accéder au site") ?>
    </a>
</p>
