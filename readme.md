# WordPress AI Content Generation

This proof of concept aims to provide a method for users to have rich, block-based content generation from an AI by providing the relevant structure and framework to provide the data needed for both the AI to select a component to use, and the correct format for WP to parse the content and generate the blocks in the editor as needed. 

## Getting Started

As a pre-requisite, you'll require **node** and **docker** to work on this project. The project is powered by `@wordpress/env` ([docs](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)) and `@wordpress/scripts` ([docs](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)) for compiling and providing a working environment.

```bash
npm install # Install Deps
npm run env start # Start the environment
npm run composer install # Install Composer deps
npm run start # Start the build/watcher
```

This will get you a local WP environemnt and start the build watcher. To stop the environment run `npm run env stop`, or check out the docs for more things you can do to manage the build process or your environment. There's _lots_ of options in the docs, so worth checking out if you're unsure how to access things like logs or to connect xdebug. 

## Configuration

After setting up the development environment, you'll need to configure your OpenAI API key:

1. Navigate to **Settings > Writing** in your WordPress admin dashboard
2. Scroll down to the "AI Content Generation" section
3. Enter your OpenAI API key in the provided field
4. Click "Save Changes"

You can get your OpenAI API key from the [OpenAI Platform](https://platform.openai.com/api-keys).

**Note:** If you're upgrading from a previous version that used `.env` files, the plugin will display a migration notice in the admin area. Simply visit the Writing settings page and enter your API key to complete the migration. 

## Working with the Content Generator

The content generation uses Action Scheduler to handle making multiple AI requests asynchronously since each request is likely to timeout. Action Scheduler doesn't always work well on local setups due to the lack of traffic. Because of this, you can run action scheduler workers manually by calling 

```sh
npm run wp action-scheduler run
```


## Running Commands

You can use your host `npm` to manage node packages. All other commands can be run inside the docker container by following [this documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/#using-composer-phpunit-and-wp-cli-tools). As a convenience, you can run `composer` by running `npm run composer`, followed by any arguments. For named arguments, you'll need to use double-double-dashes to pass the named argument to the running comand so it's not interpreted by NPM or wp-env. ie:

```bash
npm run composer install -- --no-dev
```

Typically I'd suggest running any PHP related commands as composer scripts to resolve ambiguity. You can also setup node package scripts to reference the composer scripts, allowing you to run single commands easily. Take a look at `npm run lint:php` for example. 
