# GraphQL API Crate for LotGD

[![Build Status](https://travis-ci.org/lotgd/crate-graphql.svg?branch=master)](https://travis-ci.org/lotgd/crate-graphql)

This is a basic wrapper (called a crate) around the Legend of the Green Dragon [core](https://github.com/lotgd/core). It provides a modern interface for clients utilizing [GraphQL](http://graphql.org/).

## Getting Started
To create a compatible development environment, follow the Vagrant setup instructions in the [core README](https://github.com/lotgd/core).

Once you have the VM setup, start the server like any other Symfony application:
```
php bin/console server:run
```

By default, this binds the server to 127.0.0.1, the local loopback IP. If you're using some kind
of VM setup (like Vagrant) you'll want to have it bind to 0.0.0.0:

```
php bin/console server:run 0.0.0.0:8000
```

In this case, I've explicitly specified port 8000 and that port would need to be forwarded
to your host machine to access it here.

To test it out, visit http://localhost:8000/graphiql/ on your local machine.

## Usage

To send a GraphQL query to the server, use the endpoint at `/`, instead of the typical `/graphql`. For example, if you started the server on `locahost` port 8000, then `POST` queries directly to `http://localhost:8000'.

## General GraphQL Schema

While this is not a working schema as GraphQL describes it, it is a human-readable version of it.

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
