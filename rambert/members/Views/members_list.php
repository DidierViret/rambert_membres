<div class="container" >
    <div id="list_persons" >
        <?php if (!empty($persons)): ?>
            <?php foreach ($persons as $person): ?>
                <div class="person row bg-light border-bottom border-primary pt-2 pb-2 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div><strong><a href=""><?= $person['last_name'].' '.$person['first_name'] ?></a></strong> <span class="badge badge-secondary">Catégorie</span> <span class="badge badge-warning">Accès gestionnaire</span></div>
                        <div class="small"><a href=""><?= $person['email'] ?></a></div>
                        <div class="small"><?= $person['phone_1'] ?></div>
                        <div class="small"><?= $person['phone_2'] ?></span></div>
                        <div class="small alert alert-info">Commentaire concernant la personne</div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="small"><strong><a href="">Adresse du foyer</a></strong></div>
                        <div class="small">Madame et Monsieur</div>
                        <div class="small">Karine Logodin et Didier Viret</div>
                        <div class="small">Rue et numéro 99</div>
                        <div class="small">9999 Village</div>
                        <div class="small alert alert-info">Commentaire concernant le foyer</div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="small"><strong><a href="">Fonction(s)</a></strong></div>
                        <div class="small"><strong>Commission Rosaly</strong> : membre</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="18" class="text-center">Aucune donnée disponible.</td>
            </tr>
        <?php endif; ?>
    </div>
</div>
