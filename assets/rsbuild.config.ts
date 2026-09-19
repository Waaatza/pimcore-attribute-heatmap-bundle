import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin';
import { pluginGenerateEntrypoints } from '@pimcore/studio-ui-bundle/rsbuild/plugins';
import { createDynamicRemote } from '@pimcore/studio-ui-bundle/rsbuild/utils';
import path from 'path'
import fs from 'fs';
import { v4 } from 'uuid';
import packages from './package.json'

const buildId = v4();
const buildPath = path.resolve(__dirname, '..', 'public', 'build', buildId);
const buildAssetPrefix = '/bundles/pimcoreattributeheatmap/build/' + buildId;

if (fs.existsSync(path.resolve(__dirname, '..', 'public', 'build'))) {
  fs.readdirSync(path.resolve(__dirname, '..', 'public', 'build')).forEach((file) => {
    if (file !== 'studio-npm-package.tgz') {
      fs.rmSync(path.resolve(__dirname, '..', 'public', 'build', file), { recursive: true });
    }
  })
}

if (!fs.existsSync(buildPath)) {
  fs.mkdirSync(buildPath, { recursive: true });
}

let nodeEnv = process.env.NODE_ENV;
let env: 'development' | 'production' = 'production';

if (nodeEnv !== env) {
  env = 'development';
}

export default defineConfig({
  mode: env,
  server: {
    port: 3034,
  },
  dev: {
    client: {
      host: 'localhost',
      port: 3034,
      protocol: 'ws'
    }
  },
  source: {
    entry: {
      main: './js/src/main.ts'
    },
    decorators: {
      version: 'legacy'
    }
  },
  output: {
    manifest: true,
    assetPrefix: buildAssetPrefix,
    distPath: {
      root: buildPath
    },
    filename: {
      asset: 'static/media/[name].[hash][ext]'
    }
  },
  tools: {
    bundlerChain: (chain) => {
      chain.output.uniqueName('attribute_heatmap_bundle');

      chain.module
        .rule('images')
        .test(/\.(png|jpe?g|gif|svg)$/)
        .type('asset/resource')
        .set('generator', {
          filename: 'static/media/[name].[hash][ext]'
        });
    },
  },
  plugins: [
    pluginGenerateEntrypoints(),
    pluginReact(),
    pluginModuleFederation({
      name: 'attribute_heatmap_bundle',
      filename: 'static/js/remoteEntry.js',
      exposes: {
        '.': './js/src/plugins.ts',
      },
      dts: false,
      remotes: {
        '@pimcore/studio-ui-bundle': createDynamicRemote('pimcore_studio_ui_bundle'),
      },
      shared: {
        ...packages.dependencies,
        react: {
          singleton: true,
          eager: true,
          requiredVersion: false,
        },
        'react-dom': {
          singleton: true,
          eager: true,
          requiredVersion: false,
        }
      },
    })
  ]
})