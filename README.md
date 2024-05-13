# Projektets namn

Kort beskrivning av projektet och dess syfte.

## Badges

[![Scrutinizer Build](https://img.shields.io/scrutinizer/build/g/alicebrallis/mvc.svg)](https://scrutinizer-ci.com/g/alicebrallis/mvc/build-status/master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/alicebrallis/mvc.svg)](https://scrutinizer-ci.com/g/alicebrallis/mvc/?branch=master)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/alicebrallis/mvc.svg)](https://scrutinizer-ci.com/g/alicebrallis/mvc/?branch=master)


## Innehåll

- [Om projektet](#om-projektet)
- [Klona och Starta](#klona-och-starta)

## Om projektet

Projektbeskrivning
Projektnamn: Mvc projekt

Syfte: Det här projektet handlar om att skapa en webbapplikation där användare kan spela Blackjack online. Tanken är att lära sig hur man bygger en sådan applikation med hjälp av PHP och Symfony Framework.

Teknologier: För att bygga applikationen använder vi PHP för logiken, Twig för att visa sidorna och Symfony Framework för att organisera och hantera koden.

Funktionalitet:

Spela Blackjack: Användare kan spela Blackjack-spelet direkt i webbläsaren.
Spara speldata: Applikationen använder sessions för att spara data om spelet och användaren.
Visa resultat: Efter varje spelomgång visas resultatet för användaren, till exempel om de vunnit eller förlorat.
Arkitektur:

Applikationen är uppdelad i tre delar:

Modell (Model): I mappen src/Deck och src/Card hittar vi logiken för själva Blackjack-spelet. Här finns klasser som hanterar spelets regler, beräkning av handvärden och hantering av kortleken. Modellen är ansvarig för att lagra och behandla speldata.
Vy (View): I templates mappen ligger våra Twig-filer, som ansvarar för att generera HTML-sidorna och visa användargränssnittet för spelaren. Dessa mallar renderas av webbläsaren och kommunicerar med kontrollerna för att hämta nödvändig data för att visa korrekt information för användaren.
Kontroller (Controller): I src/ProjectController använderar användarens input och styr applikationens beteende. De tar emot input från användaren och bearbetar den med hjälp av modellen och skickar sedan resultatet tillbaka till användargränssnittet för visning.
Mål:
Målet är att lära sig grunderna i hur man bygger en webbapplikation med MVC-arkitekturen. Vi vill också visa hur man använder PHP och Symfony Framework för att göra det.



## Klona och Starta

För att klona och starta projektet lokalt, följ dessa steg:

1. Klona projektet till din lokala maskin:

   ```bash
   git clone https://github.com/alicebrallis/mvc.git


   Klona projektet: Öppna terminalen och kör följande kommando för att klona projektet till din lokala maskin:
   
   git clone https://github.com/alicebrallis/mvc.git
    Navigera till projektmappen: Gå till den klonade mappen med:
    cd mvc
    
    Starta PHP:s inbyggda webbserver: Använd följande kommando för att starta den inbyggda PHP-webbservern: 
    php -S localhost:8888
    Detta kommer att starta en webbserver på din lokal dator som lyssnar på port 8888.
    Öppna webbläsaren: Öppna din favoritwebbläsare och gå till följande webbadress:
    http://localhost:8888
    Du bör se din webbapplikation visas och är redo att användas lokalt!
