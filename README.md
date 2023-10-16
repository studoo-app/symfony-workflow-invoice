# WorkFlow Invoice

Projet démo du composant workflow Symfony

## Principe du projet

Illustrer le workflow du cycle de vie d'une facture

### Diagramme du workflow
```mermaid
    graph LR
        A["Générée"]-->B["Editée"]
        B["Editée"]-->C["Envoyée"];
        C["Envoyée"]-->D["Réglée"];
        A["Générée"]-->E["Annulée"];
        B["Editée"]-->E["Annulée"];
        C["Envoyée"]-->F["Impayée"];
        
```

## Composantes du projet

### Objet Invoice

 - updatedAt - Date
 - Date - Date
 - Total - Integer
 - client - String
 - Status - String -> receptacle du workflow



