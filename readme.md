# WordPress AI Content Generation

This proof of concept aims to provide a method for users to have rich, block-based content generation from an AI by providing the relevant structure and framework to provide the data needed for both the AI to select a component to use, and the correct format for WP to parse the content and generate the blocks in the editor as needed. 

## Getting Started

As a pre-requisite, you'll require **node** and **docker** to work on this project. The project is powered by `@wordpress/env` ([docs](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)) and `@wordpress/scripts` ([docs](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)) for compiling and providing a working environment.

```sh
npm install # Install Deps
npm run env start # Start the environment
npm run start # Start the build/watcher
```

This will get you a local WP environemnt and start the build watcher. To stop the environment run `npm run env stop`, or check out the docs for more things you can do to manage the build process or your environment. 
