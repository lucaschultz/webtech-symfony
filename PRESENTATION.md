---
title: StudiBonnTask
authors:
  - Ahmad Assaf (3244771)
  - Luca Schultz (3130274)
---

Inhalt
------

1. **Einleitung**
2. **App Präsentation**
   - UI Presentation
   - Code Structure
3. **Feature Präsentation**
   - Notifications
   - Joining Teams
4. **Fazit**
   - Verbesserungen
5. **Fragen**

<!-- end_slide -->

Anforderungen
-------------

<!-- column_layout: [1, 1] -->

<!-- column: 0 -->
Must
----

- Benutzerregistrierung und Authentifizierung
- Team-Erstellung und Beitritt
- Task-Erstellung für Teammitglieder
- Task-System mit Titel und Status
- Task-Zuweisung an Benutzer
- Zugriffskontrolle
- Rollenbasierte Berechtigungen mit Symfony Security
- Symfony Forms für mindestens eine Entity
- Projektstruktur nach Best Practices

<!-- column: 1 -->

Should
-----

- Task-Metadaten
- Kommentarfunktion für Tasks
- In-App-Benachrichtigungssystem
- Benutzer-Dashboard mit anstehenden Tasks
- Task-Filteroptionen implementieren
- Access Control in Templates und Controllern

<!-- reset_layout -->

<!-- end_slide -->

Aufgaben
--------

<!-- column_layout: [1, 1] -->

<!-- column: 0 -->
Ahmad
-----

- User Authentication + Registration (Ahmad)
- Team CRUD + Join (Ahmad)
- Comment CR (Ahmad)

<!-- column: 1 -->

Luca
----

- Project Setup
- Task CRUD + Assignments
- Notification CR
- Dashboard Page

<!-- reset_layout -->

<!-- end_slide -->

Fazit
-----

**Wir hatten beide Spaß an der Arbeit an diesem Projekt.**

<!-- pause  -->

---

**Was wir anders gemacht hätten für eine *real-life* App:**

- Sicherheit
<!-- speaker_note: EMail-Validierung, Zwei-Faktor-Authentifizierung, (bessere) Validierung der Eingaben, Fehlerbehandlung sowie `404` und `500` Seiten -->
- Features
<!-- speaker_note: Passwort-Reset, Bearbeiten von Nutzerprofilen, Profilbilder, Task-Kategorisierung, Task-Zuweisung an mehrere Benutzer, Stakeholder, (Mehr) Rollen für Teammitglieder -->
- UI und UX Verbesserungen
<!-- speaker_note: Lade-Indikatoren, Animationen, generell UX Verbesserungen z.B. Hinzufügen von Teammitgliedern -->
- Code Qualität
<!-- speaker_note: (Twig) Komponenten, Tests, Linting für Codequalität, CI/CD Pipeline, besserer Code Formatter -->
