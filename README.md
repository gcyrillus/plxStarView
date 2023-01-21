# plxStarView
systeme de vote à étoiles et comptage de vues


<div id="help">
<h2>Aide du plugin plxStarView</h2>

<p>Un sujet est ouvert sur le forum de pluxml https://forum.pluxml.org/discussion/7331/plugin-plxstarview </p> 
	
<h3>Configuration</h3>
  <h3>Via l'administration </h3>
<h3>Affichage Du formulaire dans les pages 'articles'</h3>
  <p>Le formulaire est afficher par défaut</p>
  <p>Dans la page configuration du plugin, il est possible de sélectionner les articles où ne pas afficher de votes à étoiles</p>
<h3>Options d'étoiles</h3>
<p>Dans la page configuration du plugin, vous pouvez selectionné le type d'étoile à afficher :<big><big><big> &star; ou &starf; ou &#9728; ou &hearts;</big></big></big></p>
  <h3>&Agrave; partir des fichiers du thème</h3>
  <p>4 Hooks sont disponibles et leur fonctionnement est basé sur lastartlist(), ce qui vous permet de choisir les données de vos articles à affichés et d'adapté la structure HTML à vos besoins: Quelques exemples ci dessous.</p>
  <p>Il est possible d'affichée des listes triées en fonction de</p>
  <ul>
    <li>Les mieux notés:<br>Exemple en simple liste<br>
      <code>&lt;div class="stargrid mostRated" >&lt;?php   eval($plxShow->callHook('mostRated',array('div','&lt;p>&lt;a class="#art_status plxStars" href="#art_url" title="#art_title">#art_title&lt;/a>&lt;/p>'))) ; ?>&lt;/div></code></li>
    <li>Les plus vues <br> exemple en affichant l'image d'accroche, l'intro et le lien vers l'article complet:<br>
      <code>&lt;div class="stargrid mostViewed">&lt;?php  eval($plxShow->callHook('mostViewed',array('ul','&lt;li>#art_thumbnail &lt;h2>#art_title&lt;/h2>&lt;div>#art_chapo&lt;/div>&lt;a class="#art_status plxStars" href="#art_url" title="#art_title">'. L_ARTCHAPO .'&lt;/a>&lt;/li>'))) ; ?>&lt;/div></code></li>
    <li>Les moins bien notés<br>exemple en list avec image d'accroche<br>
        <code>&lt;div class="stargrid worstRated">&lt;?php   eval($plxShow->callHook('worstRated',array('ul','&lt;li>&lt;a class="#art_status plxStars" href="#art_url" title="#art_title">#art_thumbnail #art_title&lt;/a>&lt;/li>'))) ; ?>&lt;/div></code></li>
    <li>Les moins vus<br>Exemple dans une balise détails<br>
      <code>&lt;div class="stargrid lessViewed">&lt;?php  eval($plxShow->callHook('lessViewed',array('details','&lt;p>&lt;a class="#art_status plxStars" href="#art_url" title="#art_title">#art_title&lt;/a>&lt;/p>'))) ; ?>&lt;/div></code></li>
  </ul>
  <p>Par défaut 5 articles sont listés, cette option est modifiable dans l'administration</p>
  
  <h3>Inclure un formulaire pour une page statique.</h3>
   <p>Pour attribué un formulaire de vote à étoile pour une page statique, il suffit d'inserer une iframe en y passant <b>en parametre le chiffre 9999 + le numero de la page statique</b>, par exemple : <b>9999002</b> pour votre page statique numéro <b>2</b> (<b>002</b> dans l'admin).</p>
  <p>Cette Iframe peut aussi être intégré dans le template de la fonction <a href="https://wiki.pluxml.org/docs/develop/plxshow.html#lastartlist"><code>lastartlist()</code> </a> si vous souhaitez listez en tout ou partie vos articles dans une page statique ou autres endroits de votre thème.</p>
  <p>Voici un exemple listant et affichant tous vos articles de la catégorie 1 avec son image d'accroche:</p>
  <p><code>$plxShow->lastArtList('&lt;figure class="gal-item" data-theme="#art_title">#art_thumbnail &lt;figcaption>&lt;div>#art_chapo(3000) #art_content() &lt;/div>&lt;a href="#art_url#form" title="#art_title">Donner son avis&lt;/a>&lt;iframe style="grid-column:1/3;width:18em;border:none;height:5em;display:block;margin:auto;max-width: 100%;overflow:hidden;" src="./plugins/plxStarView/rateIt.php?art=#art_id">&lt;/iframe>&lt;/figcaption>&lt;/figure>', 9999, '001');</code> C'est un peu long, certe. N'hesitez pas à consulter la documentation de PluXml pour allez plus loin.</p>
<hr>
  <h3>Support</h3>
  <p> le forum de PluXml sera le bon endroit pour demander de l'aide ou y trouver la solution d'un probléme similaire au votre. C'est ici =>  <a href="https://forum.pluxml.org/">https://forum.pluxml.org/</a>. </p>
</div>
