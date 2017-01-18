# crate-www

[![Build Status](https://travis-ci.org/lotgd/crate-graphql.svg?branch=master)](https://travis-ci.org/lotgd/crate-www)

This is a basic wrapper around the Legend of the Green Dragon [core](https://github.com/lotgd/crate), a so-called crate. It provides a modern interface for clients utilizing GraphQL.

## General GraphQL Schema

While this is not a working schema as graphql describes it, it is a human-readable version of it.

### Types
```
Realm {
  url               string           URL of the realm
  name              string!          Name of the realm
  description       string           Description of the realm
  configuration     Configuration!   Realm configuration
}

Configuration {
  core              Library!         Details about the core
  crate             Library!         Details about the crate
  modules           [Library]        List of details of installed modules
}

Library {
  name              String!          Name of the library
  version           String!          version number, conforming to semantic versioning
  library           String!          Technical name of the library, in vendor/package format
  url               String           Access URL of the library, i.e., location of its code
  author            String           Author, or list of authors, of this library.
}

Session {
    apiKey          String          ApiKey for this session.
    expiresAt:      String          Date this session expires at.
    user:           User            The user account associated with this session.
}

User {
    id              String!         id of the user
    name            String!         name of the user
    characters      [Character]     A list of characters belonging to this user.
}

Character {
    id              String!         id of the character
    name            String!         Name of the character
    displayName     String!         Display name of the character (with title and colours).
}

Viewpoint {
    title           String!         Scene title
    description     String!         Scene text
    template        String          Template this scene is based on
    attachements    [String]        A list of attachments.
    actionGroups    [ActionGroup]   A list of actions groups.
}

ActionGroup {
    id              String!         id of the group
    title           String!         title of this action group
    sortKey         Int!            Sorting weight
    actions         [Action]        List of actions.
}

Action {
    id              String!         id of the action
    title           String!         title of the action.
}
```

### Queries

- Realm
- Session
- User (query `id` or `name`)
- Viewpoint (query `characterId`)
- Character (query `characterId` or `characterName`)


### Mutations
```
createPasswordUser {
                    Used to create a new user who authorizes via mail/password

    name            Name of the user account
    email           E-Mail used for this account
    password        Plain password for this account
}

authWithPassword {
                    Used to authorizes with mail/password

    email           E-Mail of the user account
    password        Password for this account
}

createCharacter {
                    Used to create a new character for a given user.

    userId          id of the user who's the owner of this character
    characterName   Chosen name for the character
}

takeAction {
                    Takes an action

    characterId     id of the character for taking the action
    actionId        id of the action that gets taken.
}
