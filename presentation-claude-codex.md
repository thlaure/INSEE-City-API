# Programmation agentique : retour d'expérience sur mes side projects

## Ce que j'ai appris, ce qui marche, ce qui casse


# Avant-propos

Ce document accompagne la présentation donnée le vendredi 24 avril 2026. Il reprend plus en détail le propos tenu à l'oral, fournit les extraits de code cités et conserve les nuances qui n'ont pas toujours la place en slide.

L'intention est double : donner aux personnes présentes un support à relire et partager, et offrir aux absents une version lisible sans le contexte oral.

Le propos n'est pas un état de l'art. C'est un retour d'expérience construit sur trois side projects personnels, avec les biais et les limites que cela implique. Les outils évoluent vite : les pratiques décrites ici ont été stabilisées au fil des mois d'utilisation quotidienne, mais elles ne sont ni figées ni universelles.

Règle de lecture : quand j'écris "je", c'est une observation vérifiée sur mes projets. Quand j'écris "on", c'est une hypothèse raisonnable à l'échelle d'une équipe. Quand j'écris "il semble" ou "probablement", c'est que je ne peux pas confirmer.

# Bloc 1 — Cadrage

## Évolution technologique : ce n'est pas la première rupture

Avant de parler d'IA agentique, un détour utile. Chaque rupture technologique majeure a suivi à peu près le même schéma : peur légitime du déplacement, opportunité réelle, période d'apprentissage collective, nouvel équilibre.

- **Imprimerie (Gutenberg, milieu du XVe siècle)** — diffusion du savoir, alphabétisation progressive, réforme religieuse, émergence de la science moderne. Avant : manuscrits copiés à la main, savoir concentré. Après : livres massivement diffusés, lecteurs massivement formés. Transition : plusieurs générations.
- **Révolution industrielle et luddites (1811-1816, Angleterre)** — des ouvriers du textile détruisent les métiers à tisser mécaniques. Le mouvement est écrasé, mais sa critique n'est pas idiote, elle porte sur la déshumanisation du travail, pas sur la machine en soi. Le terme "luddite" est souvent utilisé aujourd'hui à tort pour désigner une opposition irrationnelle au progrès.
- **Électricité et téléphone (fin XIXe, début XXe)** — transformation du rythme de vie, de la géographie du travail, apparition de nouveaux métiers (opératrice, électricien), disparition d'autres (allumeur de réverbères).
- **Informatique personnelle et Internet (années 1980-1990 pour l'adoption de masse)** — désintermédiation, nouveaux modèles économiques, explosion de l'accès à l'information. Le discours initial "pas le temps d'apprendre" a fini par céder la place à une alphabétisation numérique devenue condition d'employabilité.

L'IA et la programmation agentique s'inscrivent dans cette lignée. Les mécanismes sont les mêmes : inquiétudes légitimes (déplacement de compétences, transformation des métiers), gains productifs réels, et surtout une période d'apprentissage collective qu'on ne peut pas éviter, seulement retarder.

Message clé : le débat "pour ou contre" n'a pas de sens historique. La question structurante est "comment on s'équipe intellectuellement et professionnellement pour traverser la transition correctement".

## Ce que cette présentation n'est pas

Ce n'est pas un état de l'art. Le champ bouge trop vite et je n'ai ni le temps ni la légitimité pour prétendre couvrir ce que font toutes les équipes sérieuses du marché.

Ce n'est pas un tutoriel "comment mettre en place Claude Code en 10 minutes". L'écosystème est correctement documenté, vous trouverez les guides officiels en ressources.

Ce n'est pas une démonstration commerciale. Je n'ai aucun intérêt à promouvoir un outil plutôt qu'un autre : Claude Code, OpenAI Codex, Cursor, OpenClaw, Aider et d'autres se partagent un terrain mouvant, avec des forces différentes.

## Ce que c'est

Un retour d'expérience construit sur trois side projects personnels — Signalist, insee-city-api, fall_guardian — avec des choix documentés, des erreurs, et ce que j'en retire pour une utilisation en équipe. Aucun code provenant d'une mission professionnelle n'est cité ici. L'objectif est de donner à cette audience — développeurs, designers UX/UI, Product Managers, Product Owners et décideurs — un vocabulaire commun et des repères concrets pour se positionner vis-à-vis de ces outils.

## Pourquoi maintenant

Parce que l'usage de ces outils devient courant dans les équipes françaises. Yousign, Doctolib, Alan, Evoliz, La Boîte Immo et d'autres structures utilisent publiquement des assistants agentiques sur leurs codebases. Ne rien en faire est désormais un choix actif, pas une position neutre.

Je reviendrai sur ce point dans le bloc final dédié au coût de l'inaction.

# Bloc 2 — Thèse

## La thèse en une phrase

La programmation agentique n'est pas un outil magique qu'on utilise aveuglément, c'est une pratique d'ingénierie à construire, avec une configuration, des garde-fous et une discipline — et qui, bien faite, modifie la nature du travail de développement plutôt que son volume.

## Ce que ça veut dire concrètement

Un assistant IA brut ne connaît ni votre architecture, ni vos conventions, ni vos commandes de qualité, ni vos règles de sécurité, ni les actions qui doivent rester sous contrôle humain. Lui laisser écrire du code sans ce cadre, c'est accepter qu'il produise du code qui ressemble à du code sans être votre code.

La valeur ne vient donc pas principalement du modèle. Elle vient de la couche qu'on construit au-dessus : instructions projet, skills, slash commands, hooks, permissions explicites, fichiers sensibles exclus. Cette couche transforme un générateur de texte probabiliste en contributeur encadré par les règles de l'équipe.

## Un grand pouvoir implique une grande responsabilité

Ces outils sont puissants. Un agent bien configuré peut, en une après-midi, produire l'équivalent de plusieurs jours de travail — refactoring, implémentation de feature, génération de tests, rédaction de documentation. Cette puissance est réelle, et c'est précisément pour cela qu'elle exige une discipline accrue.

La référence est populaire — Spider-Man, Stan Lee — mais le fond est sérieux : la vitesse que procure l'outil ne dispense pas de la rigueur, elle l'exige. Un code généré qui n'est pas lu, testé, revu, versionné proprement, est un passif qui s'accumule silencieusement. L'IA n'absout pas le développeur de ses responsabilités : elle les concentre sur des étapes différentes, en amont (cadrage, prompt, règles) et en aval (revue, test, validation), plutôt qu'au milieu (écriture ligne à ligne).

C'est ce fil rouge qui structure la suite : les blocs suivants décrivent les fondamentaux techniques, puis les pratiques concrètes, puis les garde-fous qui incarnent cette responsabilité.

## Les trois axes de valeur observés

Sur mes projets, trois axes ressortent quand la couche de configuration est faite correctement :

1. **Vélocité** — non pas taper plus vite, mais passer du cadrage à un incrément de code fonctionnel en quelques itérations, avec tests et documentation cohérents.
2. **Qualité cognitive** — l'agent oblige à expliciter les règles, ce qui force l'équipe à clarifier ses propres standards. Documenter pour un agent, c'est documenter pour les humains.
3. **Cohérence** — une fois que les règles sont dans `.claude/rules/`, elles s'appliquent uniformément, ce qui réduit la dérive entre contributeurs.

Les gains sont plus limités — voire contre-productifs — quand la configuration est bâclée, sur des problèmes très nouveaux, ou sur du code sensible sans revue humaine. Le bloc 4 reviendra là-dessus.

# Bloc 3 — Fondamentaux

Ce bloc pose le vocabulaire. Je le garde court parce qu'il est nécessaire mais n'est pas le cœur du propos.

## LLM, agent, harness : trois couches qu'on confond souvent

**LLM (Large Language Model)** — le modèle lui-même : Claude, GPT, Mistral, Llama, etc. Il reçoit du texte, produit du texte, point. Il n'a pas de mémoire entre deux appels, pas d'accès à vos fichiers, pas de capacité d'exécution.

**Agent** — un LLM placé dans une boucle d'exécution, avec la capacité d'appeler des outils (lire un fichier, exécuter une commande, faire une requête HTTP) et d'itérer en fonction des résultats. C'est la boucle qui transforme un modèle en agent.

**Harness** — la couche d'orchestration autour du modèle : la boucle d'exécution, les appels d'outils, la gestion du contexte, la mémoire, les garde-fous. Claude Code, OpenAI Codex, OpenClaw, Cursor sont des harness différents, souvent capables de piloter plusieurs modèles.

Pourquoi ce vocabulaire compte : quand quelqu'un dit "l'IA ne marche pas sur mon code", la vraie question est souvent "quel harness, quel modèle, quelle configuration ?". Et quand quelqu'un annonce "le modèle X est meilleur que le modèle Y", la réponse dépend souvent davantage du harness que du modèle.

## Le dossier `.claude/` : anatomie

Le dossier `.claude/` est la couche de configuration qu'on maintient dans le repository, versionnée comme du code. Sa structure, telle que je l'applique sur mes projets, est la suivante :

- `AGENTS.md` à la racine du projet — la source canonique des instructions destinées à tout agent (Claude Code, Codex, OpenClaw). C'est le fichier que l'équipe lit et maintient.
- `CLAUDE.md` à la racine — un pointeur court qui redirige vers `AGENTS.md`. Cette dualité est un compromis d'interopérabilité : certains outils s'attendent au premier, d'autres au second.
- `.claude/settings.json` — les permissions explicites : ce que l'agent peut exécuter, ce qu'il ne peut pas. Voir extrait en section suivante.
- `.claude/rules/` — les règles projet découpées par domaine (architecture, sécurité, tests). Lues automatiquement par l'agent.
- `.claude/skills/` — les workflows réutilisables, déclenchés par description (voir ci-dessous).
- `.claude/commands/` — les slash commands, déclenchées manuellement.
- `.claude/agents/` — les définitions de sous-agents spécialisés (code reviewer, debugger, test runner...) qu'on peut déléguer depuis l'agent principal. Chaque fichier décrit un sous-agent avec son propre contexte et son propre jeu d'outils, ce qui permet d'isoler des tâches lourdes sans polluer le contexte principal. Je n'ai pas encore systématisé cet usage sur mes propres projets — à ce stade, `skills/` et `commands/` couvrent la plupart de mes besoins. À tester sur les prochaines itérations, notamment pour la revue de code et le test runner.
- `.claude/hooks/` — les points d'exécution automatiques (avant ou après une action de l'agent), typiquement en Python, pour appliquer des garde-fous déterministes.
- `.claude/patterns.md` — un fichier libre où je note les patterns récurrents du projet (conventions de nommage, structure type d'un contrôleur, forme attendue d'un test). Lisible par l'agent, maintenu par l'équipe. Ce n'est pas imposé par la convention Anthropic, c'est un choix projet.
- `.claudeignore` — les fichiers à exclure totalement du contexte de l'agent (`.env`, clés, secrets).

## AGENTS.md + CLAUDE.md : pourquoi les deux

`AGENTS.md` est la source canonique. `CLAUDE.md` est un pointeur. L'intérêt de cette dualité :

- Chaque nouvel outil du marché s'attend à l'un ou l'autre (convention ouverte côté AGENTS.md, convention Anthropic historique côté CLAUDE.md).
- On maintient un seul fichier réel. Les autres sont des pointeurs de 3 à 7 lignes.
- Quand un nouvel outil arrive, on ajoute un nouveau pointeur, on ne duplique pas les règles.

Exemple de pointeur CLAUDE.md tiré de mon projet Signalist (7 lignes) :

```markdown
# Claude Code Instructions

This file points to `AGENTS.md` for the canonical project instructions.

All agent-facing rules, architecture notes, workflows and quality
gates live in `AGENTS.md`. Maintain changes there, not here.
```

## Skills vs slash commands

Les deux formats servent à encapsuler un workflow, mais leur déclencheur diffère.

**Un skill** est déclenché automatiquement par l'agent quand la description colle à la demande. Il s'écrit dans `.claude/skills/<nom>/SKILL.md` avec un frontmatter YAML :

```markdown
---
name: prepare-commit
description: Use this skill when the user asks to prepare a commit,
  write a commit message, stage files, create a branch, prepare PR
  notes, or mentions Conventional Commits.
---

# Prepare Commit
...
```

C'est le `description` qui fait le matching. Une description bien écrite fait la différence entre un skill utile et un skill qu'on n'invoque jamais.

**Une slash command** est déclenchée manuellement par l'utilisateur, avec `/nom-de-la-commande`. Utile pour les actions rares ou dangereuses qu'on veut explicitement invoquer.

Règle de pouce que j'applique : workflow récurrent et non-dangereux → skill. Action ponctuelle, sensible, ou qui demande un cadrage humain → slash command.

## MCP (Model Context Protocol)

MCP est un protocole ouvert, publié par Anthropic en novembre 2024, qui standardise la manière dont un agent se connecte à des sources de données et des outils externes : bases de données, APIs, systèmes de fichiers, services tiers.

Sur mes projets j'utilise MCP dans deux directions :

- **Serveurs MCP externes** — par exemple pour interroger une documentation indexée, un gestionnaire de tâches, un outil de monitoring.
- **Serveur MCP interne** (projet Signalist) — exposer certaines opérations de l'API Platform aux agents, avec les mêmes contrôles d'accès que pour les utilisateurs humains.

Attention : MCP n'est pas gratuit en termes de risque. Un serveur MCP mal configuré est une surface d'attaque. Sur insee-city-api, la politique est simple : `allowedMcpServers: []`. Aucun serveur MCP autorisé par défaut, chaque exception doit être explicite et revue.

## Architecture qui aide l'agent : propre, testable, maintenable

Une observation empirique sur mes trois projets : plus l'architecture est propre, plus l'agent travaille bien. Ce n'est pas une coïncidence. Les principes qui rendent un code lisible pour un développeur humain rendent aussi un code lisible pour un agent, pour des raisons voisines : frontières explicites, responsabilités bornées, effets de bord contenus.

**Clean architecture et architecture hexagonale — frontières claires.** Sur insee-city-api, le flux d'écriture suit `UI → Application → Domain → Infrastructure`, appliqué de façon pragmatique. Quand l'agent reçoit une tâche "ajouter une feature X", il sait à l'avance quelles couches toucher et dans quel ordre. Quand l'architecture est en spaghetti, l'agent doit deviner — et il invente. Il invente parce qu'un LLM, par construction, produit toujours quelque chose de plausible ; un cadre trouble le pousse à confabuler.

**SOLID, et particulièrement SRP et DIP.** La Single Responsibility aide l'agent à localiser les changements sans casser ailleurs. L'inversion de dépendance (Dependency Inversion) lui donne des points d'injection explicites pour les tests et les substitutions. Les deux autres (OCP, LSP, ISP) aident à la compréhension du code existant, mais SRP et DIP sont ceux dont le retour sur investissement est le plus visible quand on délègue à un agent.

**Découpage en petites unités.** Un fichier de 80-150 lignes tient dans le contexte sans compression. Un fichier de 2 000 lignes oblige l'agent à résumer, à oublier des portions, à reconstruire une vue partielle. La règle que j'applique : si un fichier demande de scroller plus de deux fois, il y a probablement une responsabilité en trop. Bénéfice double : plus lisible pour l'humain, plus tractable pour l'agent.

**Testabilité.** Les tests servent de spécification exécutable. L'agent peut lire un test, comprendre l'intention, et vérifier son propre travail sans supervision continue. Sur Signalist comme sur insee-city-api, la consigne est explicite dans `AGENTS.md` : un changement sans test n'est pas un changement, c'est un prototype. L'agent a intégré la consigne, et la question "tu as ajouté les tests ?" ne se pose presque plus en revue.

**Maintenabilité.** Convention de nommage stable, imports explicites, absence de magie globale. Ces choix paient deux fois : une pour l'équipe, une pour l'agent. À l'inverse, les helpers magiques qui polluent le namespace global perdent l'agent autant qu'un nouveau développeur.

Message clé : avant de sophistiquer la configuration d'agent, il est souvent plus efficace de nettoyer la dette architecturale. L'agent ne répare pas une architecture confuse, il l'amplifie.
Autrement dit, si on fait de la merde, ça va l'amplifier.
Si on applique des bonnes pratiques, ça va amplifier la valeur qu'on apporte.

## Gestion du contexte et des tokens

La qualité d'un agent dépend directement de la qualité de son contexte. Contexte trop maigre — l'agent invente. Contexte trop gras — l'agent dilue, se perd, coûte cher. Plusieurs outils et pratiques permettent d'arbitrer.

**Ce que Claude Code fournit en natif.** Les commandes que j'utilise au quotidien :

- `/context` — affiche l'utilisation actuelle de la fenêtre de contexte, par catégorie (system prompt, messages, fichiers lus, outils). Indispensable pour savoir où on en est.
- `/compact` — demande à l'agent de résumer la conversation en cours, libère de la place tout en gardant le fil. À utiliser quand on sent le contexte se charger.
- `/clear` — efface la conversation en cours et repart d'une session neuve. Utile pour passer d'une tâche à une autre sans cross-contamination.
- Choix du modèle selon la tâche — Haiku pour des tâches simples et répétitives, Sonnet pour le quotidien, Opus pour les tâches complexes ou à fort enjeu. Le coût et la latence varient d'un ordre de grandeur entre les deux extrêmes.
- Sous-agents (via l'outil `Task` ou le dossier `.claude/agents/`) — délèguent une sous-tâche dans un contexte isolé, ce qui évite de polluer la conversation principale. Particulièrement utile pour les recherches exploratoires et les revues de code et de sécurité. C'est comme si vous demandiez à une personne extérieure au projet de faire une review de code ou de sécurité.

**Pratiques que j'applique.** 

- Garder les prompts courts et structurés.
- Donner l'intention avant les détails.
- Pointer vers des fichiers précis plutôt que coller leur contenu. Fermer une session quand la tâche est finie plutôt que laisser le contexte traîner. Et, à l'inverse, ne pas fragmenter une session en dix tâches sans rapport — le contexte partagé coûte moins cher qu'une mise en route répétée.
- Écrire en anglais, même sur des projets francophones. Les modèles sont plus performants en anglais, et les prompts plus courts.
- Utiliser des outils comme [RTK](https://github.com/rtk-ai/rtk) pour optimiser les tokens envoyés au modèle. Ça a divisé par 3 ma consommation de tokens.

**Le piège du contexte long.** Il existe un phénomène décrit dans la littérature technique sous le nom de "context rot" ou "effective context window" : au-delà d'un certain volume, la qualité des réponses de l'agent se dégrade, même si techniquement le modèle "accepte" le contexte.
L'attention se dilue, les instructions anciennes sont partiellement oubliées, les erreurs se multiplient.
Je ne peux pas citer de chiffre universel ici, le point exact de dégradation dépend du modèle, de la nature du contenu, et de la tâche.
Ce que j'observe empiriquement : au-delà d'une certaine longueur, il vaut mieux `/compact` ou `/clear`.

Message clé : le token est un budget. On arbitre comme on arbitre un temps de calcul ou un budget financier. Les équipes qui traitent ce budget avec rigueur tirent beaucoup plus de valeur des outils que celles qui laissent l'agent en roue libre.

# Bloc 4 — Retour d'expérience concret

## Le piège du FOMO et des architectures complexes

Avant de décrire ce qui marche, il faut parler de ce qui m'a fait perdre du temps.

En me baladant sur les réseaux sociaux, LinkedIn, X, Reddit, YouTube, on est exposé en permanence à des setups impressionnants : architectures multi-agents, orchestrateurs, chaînes de dizaines d'agents spécialisés, pipelines à tiroirs. C'est spectaculaire et ça donne le sentiment d'être en retard.

Je suis tombé dedans. J'ai essayé des setups multi-agents sur mes projets. Conclusion, dans mon contexte (side projects, codebases de taille modérée, travail en solo ou petite équipe) : **le gain de productivité a été négligeable** par rapport à une stack simple avec un seul agent et une configuration globale bien faite (`.claude/` structuré, skills, hooks, règles).

Ce résultat est propre à mon contexte. Sur des codebases monstrueuses, des tâches très parallélisables, ou des flux de travail où les spécialisations ont du sens, des setups multi-agents peuvent apporter. Je ne les disqualifie pas en général. Je dis simplement qu'à mon échelle, la complexité n'a pas payé.

Deux observations tirées de cette expérience :

1. **La plupart des influenceurs tech ne sont pas dans votre contexte.** Beaucoup font tourner des solo business, n'ont pas de legacy, pas de contraintes de conformité, pas de code partagé entre dizaines de développeurs, pas d'audit, pas de cycle de revue. Leur setup optimal n'est pas forcément le vôtre.
2. **Beaucoup vendent une formation, un template, un SaaS, une newsletter derrière.** Ça ne disqualifie pas leur contenu, mais ça justifie le recul. Même raisonnement pour les grandes déclarations des dirigeants de la tech — Dario Amodei (Anthropic), Sam Altman (OpenAI), Elon Musk (xAI) : leurs messages sont souvent des éléments de communication stratégique, pas des rapports d'expérience terrain.

Dit autrement : je me sers tous les jours de Claude et Codex pour coder, et je prends quand même avec recul ce que dit son CEO. C'est mon travail de développeur de garder ce recul.

La conclusion opérationnelle, que je reprendrai en clôture : on souffle, on prend le train en marche, on itère, on adapte, on s'améliore petit à petit et on reste curieux. La course à la complexité n'est pas un indicateur de sérieux.

## La stack simple qui fonctionne, sur mes projets

Sur les trois projets, la même stack minimale a donné les meilleurs résultats :

1. Un `AGENTS.md` à la racine avec l'architecture, les conventions, les commandes de qualité.
2. Un `.claude/settings.json` avec permissions explicites (allow / deny).
3. Quelques règles dans `.claude/rules/` (architecture, sécurité, tests).
4. 5 à 10 skills couvrant les workflows récurrents (scan du projet, nouvelle feature, bug fix, revue, commit).
5. Un hook Python de garde-fou pour les actions sensibles (facultatif mais précieux).
6. Un `.claudeignore` pour exclure `.env` et fichiers sensibles.

Ce n'est pas impressionnant. Ça tient dans un dossier. C'est ce qui fonctionne au quotidien.

## Un cas concret : le workflow commit / PR

C'est le workflow que j'utilise le plus. Il est encapsulé dans le skill `prepare-commit` (voir extrait en Bloc 3) et suit ce déroulé :

1. L'agent lit `git status` et `git diff` pour comprendre le changement réel.
2. Il `git add` les fichiers concernés (sans assumer que tout ce qui est modifié doit être commité).
3. Il lance les commandes de qualité du projet : `make quality`, `make tests`, `make security`, pour vérifier que le changement passe.
4. Il relit le changement sous angle correction, architecture, validation, couverture de tests.
5. Il relit une seconde fois sous angle sécurité.
6. Si on est sur une branche protégée (main, master, develop), il crée d'abord une branche de travail.
7. Il produit un message de commit au format Conventional Commits, avec un titre et un corps concis.
8. Il prépare des notes de PR structurées : quoi, pourquoi, comment, tests, risques ou follow-up.
9. Il liste une checklist de vérification basée sur les vraies commandes du repo.
10. Il ne commit **jamais** sans confirmation explicite. Il ne push **jamais** sans confirmation explicite.

Deux règles absolues inscrites dans le skill lui-même : "Never commit or push without explicit confirmation in the current conversation" et "Do not assume all changed files belong to the intended commit". Ces règles sont versionnées, relues, modifiables par l'équipe.

## Ce qui ne marche pas, ou pas encore

Je tiens à lister honnêtement les zones où ma pratique a buté.

**Les très gros refactorings structurels.** Sur un changement qui touche plus de quelques dizaines de fichiers avec des invariants croisés, l'agent perd le fil du contexte, produit des changements localement corrects mais globalement incohérents. La solution que j'applique : découper en étapes validables, garder l'humain comme orchestrateur.

**Le code très nouveau ou très spécialisé.** Sur du code où l'état de l'art est mal documenté publiquement, ou sur des domaines très métier, l'agent invente des solutions plausibles mais fausses. La règle : plus le domaine est spécifique, plus la revue humaine doit être rigoureuse.

**Les décisions d'architecture structurantes.** Choisir entre deux patterns, décider d'un découpage de bounded context, arbitrer une dette technique : l'agent peut préparer les options, il ne doit pas trancher. C'est explicitement exclu dans mes règles d'architecture.

**La gestion des dépendances.** `composer update`, `npm install` de nouveaux packages, changements de version majeure : bloqués côté permissions dans tous mes projets. Ce sont des décisions trop lourdes pour être automatisées.

## Ce qu'il reste à tester : une approche TDD depuis les critères d'acceptance

Expérimentation à venir sur mes projets, donc présentée sans prétention de résultat.

L'idée est simple : partir de critères d'acceptance (AC) d'une user story, demander à l'agent de les traduire en tests qui échouent, puis d'implémenter le code qui fait passer ces tests — ordre strict, pas l'inverse. Le TDD classique, appliqué à un agent.

Hypothèses que je veux vérifier :

- **Exactitude fonctionnelle.** Forcer l'agent à écrire les tests avant le code devrait réduire la tendance à écrire du code qui "a l'air correct" mais ne correspond pas exactement à l'intention. Les tests deviennent un cahier des charges exécutable, pas quelque chose ajouté après coup.
- **Traçabilité produit → code.** Chaque AC devient un test nommé, associé au code qu'il couvre. La revue devient plus simple : on lit les AC, on lit les tests, on vérifie la concordance.
- **Moins d'oublis.** Les AC couvrent habituellement des cas limites que l'agent oublie quand il écrit le code d'abord. Avec l'ordre inversé, les cas limites sont nommés dès le début.

Limites que j'anticipe et que je veux mesurer :

- La qualité d'un AC est variable. Un AC mal écrit donne un test mal écrit, qui passe à tort. Cette approche déplace la rigueur en amont, elle ne l'élimine pas.
- Le découpage des tests est un arbitrage non trivial. Trop fins, les tests deviennent fragiles. Trop gros, ils perdent leur valeur de spécification.
- La boucle "test rouge → implémentation → test vert" est plus lente en nombre d'itérations agent, donc plus coûteuse en tokens. Il faut mesurer si le gain en exactitude compense le coût.

Protocole envisagé : appliquer cette approche sur une série de petites features sur Signalist, comparer avec cette méthode actuelle (implémentation puis tests), et tenir un journal. Présentation de ces résultats, si significatifs, dans une version ultérieure de ce support.

Honnêteté : je ne peux pas affirmer aujourd'hui que cette approche marche mieux dans mon cas. Je l'affirme comme hypothèse à tester.

## Dette silicium, dette cognitive, dette épistémique

Trois concepts utiles pour nommer des risques réels.

**Dette silicium** (formulation attribuée à Peter Steinberger et d'autres dans la communauté) — le coût futur du code généré rapidement sans revue approfondie. Ce code peut compiler, passer les tests, et être structurellement fragile. Il accumule une dette qui ne se voit qu'à la maintenance, six mois plus tard, quand il faut le modifier.

**Dette cognitive** — la perte de compréhension fine qu'un développeur construit d'habitude en écrivant son code ligne à ligne. Si l'agent écrit tout, le développeur perd le contact avec le détail. Il peut valider une PR qu'il ne comprend pas vraiment. Je ne peux pas confirmer chiffres à l'appui l'ampleur du phénomène, mais je peux le confirmer empiriquement sur moi-même : quand je délègue trop, je relis moins bien.

**Dette épistémique** — à l'échelle d'une équipe : la perte collective de savoirs lorsque la rédaction et la maintenance du code sont externalisées à l'agent. Le savoir ne s'ancre plus dans les têtes, mais dans les prompts et les logs. Le jour où on change d'outil, on peut se retrouver plus démuni qu'avant.

Ces trois dettes ne sont pas une raison de ne pas utiliser ces outils. Elles sont une raison de les utiliser consciemment, avec des contrepoids explicites, d'être plus exigent avec nous-même que je décrirai dans les blocs 5 et 6.

# Bloc 5 — Garde-fous

Dans ce bloc, je reviens à la formule du bloc 2 : "un grand pouvoir implique une grande responsabilité". Les garde-fous ne sont pas là pour brider l'outil. Ils sont là parce que le pouvoir est réel.

## Permissions explicites : allow / deny

Extrait du `.claude/settings.json` du projet insee-city-api :

```json
{
  "$schema": "https://json.schemastore.org/claude-code-settings.json",
  "allowedMcpServers": [],
  "permissions": {
    "allow": [
      "Bash(make lint:*)",
      "Bash(make analyse:*)",
      "Bash(make tests:*)",
      "Bash(make security:*)",
      "Bash(git status:*)",
      "Bash(git diff:*)",
      "Bash(git add:*)",
      "Read",
      "Write",
      "Edit"
    ],
    "deny": [
      "Bash(git push:*)",
      "Bash(composer require:*)",
      "Bash(composer update:*)",
      "Bash(rm -rf:*)",
      "Bash(curl:*)",
      "Bash(wget:*)",
      "Read(./.env)",
      "Read(./.env.*)"
    ]
  }
}
```

Principe : tout ce qui n'est pas autorisé est interrogé à l'utilisateur. Tout ce qui est interdit est bloqué, sans négociation. Les commandes de qualité du projet (`make ...`) sont autorisées parce qu'elles sont sûres et utiles. `git push`, `composer update`, `rm -rf`, `curl`, `wget` sont bloquées par défaut.

## Hooks Python : garde-fous déterministes

Dans Signalist et fall_guardian, j'ai ajouté un hook Python qui s'exécute automatiquement avant certaines actions. Extrait simplifié du hook Signalist :

```python
REPO_ROOT = Path(__file__).resolve().parents[2]
PROTECTED_BRANCHES = {"main", "master", "develop"}
SENSITIVE_SURFACE_PREFIXES = (
    "src/Domain/Auth/",
    "src/Infrastructure/Auth/",
    "src/Infrastructure/AI/",
    "src/Infrastructure/MCP/",
    "config/packages/security",
    "config/routes",
)
ENV_FILE_PATTERN = re.compile(r"(^|/)\.env(\..+)?$")
```

Le hook bloque les écritures sur les branches protégées, alerte sur les modifications dans les zones sensibles (auth, sécurité, routes, configuration MCP), et bloque l'accès aux fichiers `.env`.

Pourquoi un hook Python plutôt qu'une simple règle dans `AGENTS.md` : parce qu'une règle en langage naturel peut être mal interprétée ou oubliée là où le hook est déterministe.

## Règles projet lisibles

Extrait de `.claude/rules/security.md` (insee-city-api) :

```markdown
- Never hardcode secrets, tokens, or credentials.
- Enforce authorization on the server side when access is scoped.
- Validate user-controlled input before database writes or outbound calls.
- Minimize response data to the fields actually intended for clients.
- Preserve the repository's existing error-handling strategy.
- Do not weaken static-analysis protections to make a warning disappear.

## AI Tool & MCP Policy

- MCP servers are blocked project-wide (allowedMcpServers: [] in settings.json).
  Any exception requires explicit team approval.
- Do not paste proprietary business logic into external AI tools
  or web interfaces (ChatGPT, Copilot Chat, etc.).
- Sensitive file classes are off-limits for AI context:
  .env, private keys, and any file matched by .claudeignore.
- When in doubt, ask a senior engineer before using AI assistance
  on code touching authentication, rate limiting, or external integrations.
```

Ces règles sont versionnées comme du code. Elles passent par PR. Elles sont relues par l'équipe. Elles servent à la fois à l'agent et aux humains.

## Fichiers sensibles exclus : `.claudeignore`

Sur le même modèle que `.gitignore`, le fichier `.claudeignore` liste les fichiers et dossiers totalement exclus du contexte de l'agent. `.env`, clés privées, dumps de base, certaines configurations. L'agent ne les lit pas, ne peut pas les exposer par inadvertance.

## Revue humaine systématique

Aucune des pratiques précédentes ne supprime la revue humaine. Elles la rendent plus ciblée.

Mon flux personnel sur mes projets :

1. Je dirige l'agent sur un changement cadré (une feature, un fix, un refactoring délimité).
2. L'agent produit un diff.
3. Je relis **intégralement** le diff avant `git add`.
4. Je lance les tests et les checks qualité.
5. Je relis une seconde fois avec un œil sécurité si le changement touche une surface sensible.
6. Je confirme le commit, puis le push.

En équipe, la revue humaine par un pair s'ajoute à la revue personnelle. L'agent ne remplace ni l'une ni l'autre.

## Workflows explicites via skills

Les skills encodent des procédures dont on ne veut pas s'écarter. Extrait du skill `prepare-commit` déjà cité, focalisé sur la partie "rules" :

```markdown
Rules:
- Never commit or push without explicit confirmation in the current conversation.
- Do not assume all changed files belong to the intended commit.
- Keep the commit message human-readable and reviewer-friendly.
- Prefer the repository's real verification commands over generic
  checklist items.
- If pre-commit checks find blockers, report them before proposing
  the final commit.
```

Ces règles en langage naturel s'ajoutent aux permissions techniques. Elles ont surtout un rôle pédagogique : elles expliquent à l'agent (et aux humains qui lisent le fichier) pourquoi la procédure est ce qu'elle est.

# Bloc 6 — Impact humain et organisationnel

La section précédente était technique. Celle-ci parle de pratiques d'équipe. Je considère qu'elle est au moins aussi importante.

## Onboarding des juniors : période d'appropriation sans IA

Sur ce point, j'ai une position tranchée. Un développeur junior qui arrive sur un projet, et plus encore un développeur frais émoulu d'école, doit avoir une période d'appropriation du code **sans assistant IA génératif**.

Durée indicative sur la base de ce que j'observe :

- **Sortie d'école, premier poste** : 3 à 6 mois sans génération de code par IA. L'usage en lecture ou en explication peut être autorisé plus tôt. L'objectif est d'évaluer et de construire les qualités individuelles comme la compréhension du code, intuition de l'architecture, capacité à débugger, avant que l'outil ne les masque.
- **Senior nouveau sur le projet** : 1 à 3 mois avant pleine autonomie avec l'agent, le temps d'absorber les conventions, la structure, les subtilités du domaine.

Les durées sont à calibrer selon la complexité du projet. Ce qui ne change pas, c'est le principe : l'IA amplifie ce qu'un développeur sait déjà faire. Un junior qui n'a pas construit sa propre compréhension risque de devenir dépendant sans développer la capacité de relecture critique qui, elle, prend du temps.

Précision importante : ce n'est pas une punition, c'est une période d'appropriation.

Ce sont des éléments qui peuvent être débattus. Je l'assume, en sachant que :

- L'environnement natif d'un junior en 2026 inclut l'IA, et le priver peut sembler artificiel.
- Une période de ralentissement est un coût réel pour l'équipe.
- Il existe d'autres leviers d'évaluation (pair programming, revues, exercices dédiés).

Je pense néanmoins que le risque de laisser s'installer une dépendance précoce est supérieur au coût du ralentissement initial.

## Journée sans IA, hebdomadaire

Pratique complémentaire, pour les développeurs déjà expérimentés avec l'agent : une journée par semaine sans assistant génératif.

Objectif : maintenir les compétences. Garder la capacité d'écrire du code à la main, de débugger à la main, de lire une stack trace sans demander à l'agent de la résumer. C'est le pendant des exercices pour un sportif professionnel qui fait aussi de la préparation physique générale.
J'ai pu constater de mon côté, qu'en gardant les mains dans le code régulièrement, je me rends compte de certaines erreurs qui ont été produites par un agent, en pratiquant et utilisant le code produit.
Ça me permet de faire évoluer les fichiers d'instructions et de règles pour éviter que l'agent ne reproduise les mêmes erreurs à l'avenir.

Ce n'est pas de la nostalgie. C'est de la préservation de capacités qui, perdues, sont longues à reconstruire, surtout du côté de l'effort cognitif.

## Le développeur change de rôle

Ce que je vois changer dans mon propre travail :

- **Moins d'écriture ligne à ligne**, plus de cadrage en amont (prompts précis, règles claires, découpage des tâches).
- **Plus de revues**, et une revue plus ciblée : l'agent produit vite, il faut relire aussi vite.
- **Plus d'architecture explicite**. Documenter devient rentable : les règles documentées bénéficient à la fois aux humains et à l'agent.
- **Un rapport au code plus orchestral qu'artisanal**. Je dirige, je vérifie, je corrige. J'écris encore à la main, mais moins souvent, pour des cas précis.

Ce changement n'est pas neutre. Il favorise les développeurs qui ont déjà une bonne vision d'ensemble et peut être déroutant pour ceux qui tirent leur valeur principalement de la vitesse de frappe ou de la mémoire précise de syntaxes.

## Impact sur les autres métiers

**Designers UX/UI.** Les agents qui génèrent des maquettes ou des prototypes existent, mais le point de contact le plus immédiat pour les designers est ailleurs : les agents accélèrent le prototypage frontend, ce qui raccourcit la boucle design → dev. Le travail des designers gagne en poids (plus de décisions en amont) et change de nature (plus de critique du prototype généré).

**Product Managers et Product Owners.** L'écart entre une spec écrite et une implémentation explorable se réduit. Un PO qui sait rédiger précisément peut obtenir en quelques heures un prototype fonctionnel. Cela renforce l'exigence sur la qualité d'écriture des user stories, des critères d'acceptance, de la priorisation. Moins d'ambiguïtés tolérables.

**Décideurs.** Les projections sont plus tendues. Les gains de vélocité sur certains chantiers sont réels et documentables, mais très inégalement distribués. Les risques de dette silicium, de compliance et de sécurité augmentent si l'usage n'est pas encadré. Point concret : la politique IA devient une composante explicite du socle technique, au même titre que la politique de sécurité ou d'observabilité.

## Souveraineté et auto-hébergement

Sujet sensible et légitime, en particulier pour les structures françaises et européennes.

Les modèles frontière actuels (Claude, GPT, Gemini) sont hébergés aux États-Unis, mais aussi ceux de Mistral. Pour des projets contenant des données sensibles, cela pose des questions réelles :

- confidentialité des prompts et du code envoyé,
- conformité RGPD,
- dépendance à un fournisseur étranger,
- coût de sortie si le tarif ou les conditions évoluent.

Les pistes existantes :

- **Modèles open-weight exécutés localement** (famille Llama, Qwen, Mistral open-weight, DeepSeek, etc.). Performances en hausse mais toujours très en retard sur les modèles frontière fermés.
- **Fournisseurs européens** comme Mistral AI, qui propose des modèles sous différentes licences, mais les offres européennes restent encore hébergées aux USA, et ont globalement beaucoup moins performantes pour un coût similaire.
- **Auto-hébergement sur infrastructure maîtrisée** avec des modèles open-weight. Faisable techniquement, coûteux en GPU, compétences et en maintenance. Justifiable pour des périmètres très sensibles.

Sur ce sujet, l'ANSSI a publié en 2026 une note qui cite nommément des outils de développement agentique (Claude Cowork et OpenClaw sont mentionnés dans la communication).
Les recommandations faites n'étaient ni plus ni moins que des bonnes pratiques SSI, rien de bien particulier sur ce qui est faisable sur les agents, ça montre aussi que les grandes instances sont encore très loin de ces sujets.

## Intégrer l'IA dans un produit : discipline et souveraineté

Deux messages distincts qui se croisent, à l'attention des décideurs et des équipes produit.

**Discipline entrepreneuriale.** Mieux vaut se concentrer sur les sujets qu'on maîtrise vraiment que s'improviser sur des domaines qu'on ne connaît pas. En France, une sortie de piste entrepreneuriale coûte cher : fiscalité de l'échec, rebond plus lent, accès au capital plus difficile qu'ailleurs. L'IA attire toutes les attentions en ce moment, ce n'est pas une raison pour se réinventer sur un domaine où on n'a pas les compétences. Utiliser l'IA comme outil de productivité interne et construire un produit qui intègre de l'IA sont deux décisions de nature très différente.

**Souveraineté au niveau du produit.** Il y a une distinction importante à faire entre :

- utiliser Claude, Codex ou Cursor comme outils internes de développement (les risques portent sur le code),
- intégrer de l'IA dans le produit vendu aux clients (les risques portent sur les données des clients et sur la dépendance fournisseur du produit lui-même).

Pour le second cas, et particulièrement si on construit un système de type RAG (Retrieval-Augmented Generation) où les documents et données des clients transitent par le modèle, il vaut la peine de se poser sérieusement la question des acteurs nationaux ou européens. Mistral AI pour des modèles européens, hébergement maîtrisé pour des données sensibles, stack RAG avec indexation contrôlée. La décision dépend de la criticité des données, du secteur, des engagements contractuels envers les clients.

Ce qui est un outil interne n'a pas la même criticité qu'une IA intégrée au produit vendu. C'est une distinction que les décideurs ont intérêt à faire explicitement.

## Impact environnemental et énergétique de l'IA

Ce n'est pas le sujet, mais il revient systématiquement, donc je ne peux pas l'éviter. 
Position courte, assumée : les chiffres grand public qui circulent sont pour la plupart mal sourcés ou obsolètes. Voici un filtre de lecture, pas un plaidoyer.

Je précise que j'ai une sensibilité particulière à ce sujet, et que, sans vouloir me jeter des fleurs, mon niveau de connaissance sur le sujet dépasse largement ce qu'on peut entendre dans les conversations grand public. J'ai lu une bonne partie de la littérature académique, suivi les débats dans la communauté technique, et je suis conscient des enjeux.

Les quatre chiffres qu'on entend le plus, et ce qui cloche :

- **"500 ml d'eau par requête"** : trace vers Shaolei Ren et al. (2023). Concerne GPT-3, pas les modèles actuels. "Par conversation de 20-50 requêtes", pas "par requête". Varie d'un ordre de grandeur selon le datacenter.
- **"×10 l'énergie d'une recherche Google"** : estimation Goldman Sachs / IEA 2024. Compare ChatGPT à un Google pré-AI Overview. Agrège des requêtes courtes et des conversations longues dans un seul ratio.
- **"X voitures à vie pour entraîner un modèle"** : trace vers Strubell et al. (2019) sur BERT. Ne s'applique pas à GPT-4 : les ordres de grandeur et l'infrastructure ont changé.
- **"L'IA = X % de l'électricité mondiale en 2030"** : extrapolations à partir de rapports IEA qui couvrent **tous** les datacenters, pas seulement l'IA. Projections régulièrement révisées, toujours à la baisse côté IA, en hausse côté agrégat datacenters.

Ce qui reste vrai, sans dramatisation :

- L'entraînement a un coût carbone significatif et ponctuel, qui s'amortit sur l'usage.
- L'inférence coûte peu par requête, l'enjeu est l'agrégat.
- Les datacenters consomment de l'eau, fortement variable selon la localisation.
- Les gains d'efficacité sont réels et rapides (modèles plus petits, quantization, accélérateurs).
- Les fournisseurs ne publient pas de bilan auditable par requête. Toute comparaison précise est reconstruite.

Ne pas oublier que ces outils nous permettent aussi de faire des choses qui étaient impossibles avant, et que le bilan global doit prendre en compte les bénéfices, pas seulement les coûts.

## Usage réel dans les entreprises françaises

Plusieurs structures françaises utilisent publiquement des assistants agentiques : YouSign, Doctolib, Alan, Evoliz, La Boîte Immo. Les usages varient selon la taille, le secteur, et la culture de l'entreprise.

# Bloc 7 — Coût de l'inaction, message aux sceptiques, recommandations

## Pourquoi parler de coût de l'inaction plutôt que de menace

Je choisis délibérément de parler de **coût de l'inaction** plutôt que de **menace concurrentielle**. La nuance compte :

- Une menace suggère une urgence anxiogène, souvent mal utilisée comme levier de décision.
- Un coût est mesurable, arbitrable, négociable. Il permet une conversation rationnelle.

Les postes de coût de l'inaction, tels que je les observe :

1. **Retard sur la vélocité de référence du marché.** Les équipes qui utilisent bien ces outils livrent plus vite sur certains chantiers. Ne pas les utiliser revient à accepter un écart relatif.
2. **Difficulté de recrutement.** Les développeurs qui utilisent ces outils au quotidien choisissent préférentiellement des équipes qui les autorisent et les encadrent.
3. **Pression clients.** Certains clients et partenaires demandent explicitement comment l'équipe utilise l'IA, au titre de la sécurité, de la conformité, ou de la vélocité attendue.
4. **Dette d'organisation.** Plus on attend, plus l'intégration devient lourde : changement de pratiques, formation, mise en place des garde-fous, réécriture de processus.

Ces coûts ne justifient pas une adoption précipitée ou non-encadrée. Ils justifient une position claire : oui ou non, et si oui, avec quelles règles.
C'est aussi pour cette raison que je prends des initiatives sur mon temps libre avec une licence personnelle Claude Code et Codex : pour expérimenter, apprendre, et être prêt à encadrer une adoption professionnelle quand elle se présentera.

## Message aux sceptiques

Cette partie s'adresse directement à celles et ceux qui, dans l'audience, voient ces outils avec méfiance, fatigue ou hostilité. C'est une position légitime. Je ne cherche pas à convertir, je cherche à partager comment j'en suis venu à une position différente.

Aujourd'hui, je suis optimiste sur le progrès humain. Pas d'une manière naïve, je reconnais les chamboulements en cours, les risques de concentration de pouvoir, les questions de souveraineté, de consommation énergétique, de qualité informationnelle. Ces questions sont réelles et il est légitime de les poser.

Mais je pense qu'il vaut mieux apprendre à utiliser ces outils que les ignorer. Pour deux raisons simples :

1. **La technologie est là et ne disparaîtra pas.** Refuser individuellement ou collectivement l'usage ne change rien à la trajectoire globale. Cela ne fait que soustraire la discussion à celles et ceux qui ont le plus de recul pour l'orienter.
2. **Être proactif vaut mieux qu'être subi.** Les règles, les garde-fous, les politiques — tout ce qui est décrit dans cette présentation — se construit avec les gens qui utilisent les outils, pas contre eux.

Dernier point, plus personnel. Il y a un réflexe culturel français, que je connais pour l'avoir moi-même pratiqué, qui consiste à vouloir tout contrôler et tout contenir avant d'expérimenter. Ce réflexe protège parfois. Il freine souvent. Sur ce sujet, je pense qu'on gagne à inverser l'ordre : expérimenter d'abord sur des périmètres petits et maîtrisés, apprendre, puis cadrer. Plutôt que cadrer sans avoir rien essayé.

Rester curieux, prendre le train en marche, itérer, adapter, améliorer petit à petit. Ne pas se laisser happer par le FOMO, ne pas se laisser pétrifier par la peur. Entre ces deux pièges, il y a une troisième voie : celle de la pratique informée.

## Recommandations par profil

Chacun des profils présents dans l'audience peut repartir avec une action concrète à son échelle.

**Si vous êtes développeur ou développeuse** :

1. Expérimentez sur un side project ou un périmètre non-critique. Pas sur le code production de l'équipe, tant que la politique n'est pas cadrée.
2. Installez un `AGENTS.md` avec vos conventions, un `.claude/settings.json` avec des permissions explicites, un `.claudeignore`.
3. Écrivez vos premiers skills pour les workflows que vous répétez.
4. Gardez une journée par semaine sans assistant.
5. Relisez tout ce que l'agent produit. Toujours.
6. Appliquer les bonnes pratiques qui ont trop souvent été délaissées.

Vous pouvez vous inspirer de mes projets et même proposer des améliorations ou des contributions. Je suis aussi disponible pour échanger sur les difficultés que vous rencontrez.

**Si vous êtes designer UX/UI** :

1. Je préfère ne pas vous donner des recommandations techniques sur les outils de design génératif, car je ne les maîtrise pas assez. Je vous encourage à suivre les nouveautés dans ce domaine, à expérimenter avec les outils qui sortent, et à partager vos retours d'expérience.

**Si vous êtes Product Manager ou Product Owner** :

1. Tirez bénéfice du fait qu'une spec mieux écrite mène à un prototype plus rapide.
2. Investissez dans la qualité de vos critères d'acceptance. Les ambiguïtés sont plus coûteuses qu'avant.
3. Soyez un garde-fou pour l'équipe sur les priorités : éviter que l'agent n'amène à implémenter "parce qu'on peut" plutôt que "parce qu'il faut".

**Si vous êtes décideuse ou décideur** :

1. Demandez à l'équipe technique une note d'une page : quels outils utilisés, quelles permissions, quels garde-fous, quelle politique sur les données sensibles.
2. Investissez dans l'encadrement (formation, revue, règles projet) avant la vélocité. Les gains de vélocité non-encadrés reviennent comme dette.
3. Suivez les publications officielles : ANSSI en France, recommandations sectorielles. La conformité devient une composante du choix d'outil.

## France, R&D, pragmatisme : l'éthique est la cerise, pas le gâteau

Un angle qui me tient à cœur. La France a une tradition mathématique et scientifique solide. Elle compte 13 médaillés Fields, ce qui la place au 2e rang mondial derrière les États-Unis selon l'International Mathematical Union (chiffre à confirmer avec la source officielle IMU avant citation publique, voir Annexe C). Les capacités de R&D, la pensée libérale des Lumières, la densité d'écoles d'ingénieurs, et le tissu académique sont réels.

Le risque que j'observe dans le discours public sur l'IA n'est pas l'excès de régulation en tant que tel. C'est le nivellement par le bas qui se produit quand la conversation se concentre presque exclusivement sur les risques, l'éthique, les inquiétudes, au détriment de l'usage réel, de la maîtrise technique et de la production.
Et comme souvent, ce sont les personnes qui ont le moins de connaissances sur les sujets qui s'y opposent.

L'éthique est importante. Mais elle est la cerise sur le gâteau. Elle vient consolider un gâteau qui existe : des usages, des compétences, des produits, des retours d'expérience. Sans production et sans usage, l'éthique seule ne construit pas un écosystème. Elle ne fait que commenter celui des autres.

Position assumée, à opposer et à critiquer :

> Prendre ces outils au sérieux, les utiliser, les comprendre en profondeur, est la meilleure défense du modèle français. Ne pas les utiliser par principe est un affaiblissement volontaire. Les pays qui produiront de la maîtrise technique garderont une voix qui compte dans les débats sur l'éthique. Les autres commenteront.

## Veille, sources, références que je suis

Pour rester à jour sur un champ qui bouge vite, il faut une routine de veille explicite. Cette slide est volontairement laissée ouverte : chacun construit sa liste selon son profil et ses centres d'intérêt. Je présenterai la mienne à l'oral.

Je vous partage quelques sources que je suis régulièrement, pour suivre les évolutions dans la tech :

- Blogs (Anthropic, OpenAI, Google DeepMind, Hackernews, Techmeme).
- Formations (Anthropic propose une série de formations gratuites sur l'agentique, elles sont plutôt courtes).
- Newsletters thématiques (TLDR notamment).
- Comptes individuels à suivre (chercheurs, praticiens, DevRel), en évitant le bruit marketing.
- Podcasts et conférences (Silicon Carne, TBPN, The Pragmatic Engineer, a16z, Lenny's Podcast, etc.).

Principe transverse : diversifier les angles (technique, produit, éthique, économique, souverain), apprendre des choses sur les domaines transverses au nôtre, et se réserver du temps de veille hebdomadaire explicite, pas en fond de tâche.
Mais garder du temps pour soi.
Il est important de savoir qu'il est de plus en plus important de développer un niveau de connaissance élargi, sur tous les sujets, pour développer un profil plus général, tout en conservant sa spécialisation technique.
Je peux vous recommander une série de lectures pour développer cette culture générale :
- How the world really works de Vaclav Smil, pour comprendre les grands systèmes techniques et énergétiques.
- Not the end of the world de Hannah Ritchie, pour une perspective critique sur les discours apocalyptiques.
- Thinking, Fast and Slow de Daniel Kahneman, pour comprendre les biais cognitifs et les mécanismes de décision.

De mon côté je fais énormément de veille, je partage les choses que je trouve pertinentes pour nous sur notre espace de veille collaborative Enovacom R&D sur [daily.dev](https://dly.to/02T2P2ZpEuI)

## Pour conclure

La programmation agentique n'est pas une baguette magique. Ce n'est pas non plus une mode passagère. C'est un outil d'ingénierie puissant qui demande à être encadré comme une pratique d'ingénierie.

La formule revient : un grand pouvoir implique une grande responsabilité. Les garde-fous qu'on met en place ne sont pas des freins, ce sont les conditions pour utiliser l'outil sérieusement.

Les gains que j'observe, sur mes propres projets, sont réels. Les risques sont réels aussi. Entre les deux, il y a une couche de configuration, de règles, de revue humaine.
C'est cette couche qui fait la différence entre un usage professionnel et un usage amateur.

Je termine là-dessus : la question n'est plus "faut-il utiliser ces outils". La question est "comment les utiliser bien".