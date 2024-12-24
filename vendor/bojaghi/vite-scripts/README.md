# Vite Scripts

워드프레스에서 Vite 번들러를 이용한 스크립트 삽입을 위한 라이브러리입니다.

## 목적

워드프레스에서 Vite 번들러 기반의 자바스크립트, 또는 타입스크립트를 삽입할 때 다소 문제가 있습니다.
기본적으로 Vite 번들러는 SPA 기반의 웹사이트를 가정하며 index.html 파일을 기반으로 동작하게 레이아웃이 구성되어 있습니다.
또한 SPA 방식의 번들링은 여러 엔트리를 지원해야 하는 워드프레스의 어울리지 않습니다.

물론 [Backend Integration](https://vite.dev/guide/backend-integration.html)을 참조하여 여러 엔트리를 지원하도록
만들 수 있습니다. 그러므로 Vite Script 패키지는 이런 지원을 일괄적으로 담당하여 보다 빠르고 편리한 코드 작성을 도와줍니다.

## 사용 시작하기

React를 사용한다고 가정하고 작성하였습니다.

### 설치하기

`composer require bojaghi/vite-scripts` 로 설치합니다.

### 레이아웃 설정

최초 레이아웃 실행은 [Scaffolding Your First Vite Project](https://vite.dev/guide/#scaffolding-your-first-vite-project)
문서를 참고합니다.

플러그인이나 테마 루트에 Vite 번들러를 사용하기 위한 셋업을 진행합니다.
아래 명령을 플러그/테마 외부의 임의의 디렉토리에서 실행합니다.

`pnpm create vite sample-project --template react-swc` (또는)
`pnpm create vite sample-project --template react-swc-ts` (타입스크립트를 사용)

이렇게 하면 해당 디렉토리 아래에 `sample-project` 디렉토리가 생성되며, SPA 기반 Vite 초기 템플릿 코드가 복제됩니다.
그러면 `sample-project` 디렉토리에 생성된 아래와 같은 여러 .js, .json, .ts 파일들을 작성 중인 플러그인이나 테마로 복사합니다.

- eslint.config.js
- package.json
- tsconfig.app.json
- tsconfig.json
- tsconfig.node.json
- vite.config.ts

src, public 디렉토리는 복사하지 않습니다.

### 설정 파일 수정

`vite.config.ts` 또는 `vite.config.js`를 제외한 나머지 설정 파일은 기본값으로 두고, 프로젝트의 필요에 따라 적절히 조정합니다.
워드프레스에서 여러 자바스크립트 엔트리를 활용하기 위해서는 `vite.config.ts (또는 .js)` 파일을 수정하는 것이 필요합니다.

```typescript
import {defineConfig} from 'vite'
import react from '@vitejs/plugin-react-swc'

// https://vite.dev/config/
export default defineConfig({
    build: {
        manifest: true,
        modulePreload: {
            polyfill: true,
        },
        rollupOptions: {
            input: [
                'src/script-1.tsx',
                'src/script-2.tsx',
            ],
        }
    },
    publicDir: false,
    plugins: [react()],
})
```

- `build.manifest`를 `true`로 설정하여 반드시 manifest.json 파일을 생성합니다.
- `build.modulePreload.polyfill`을 `true`로 설정합니다. 개발 모드로 작업할 때 필요합니다.
- `build.rollupOptions.input` 값에 자바스크립트 진입점을 플러그인 또는 테마 루트 디렉토리의 상데 경로로 입력합니다
- `publicDir`은 필요없으므로 `false`로 설정합니다.

위 코드는 워드프레스에서 스크립트를 사용하기 위한 최소 설정입니다.
[Vite 공식 문서](https://vite.dev/config/)에 따라 적절히 프로젝트 요구 사항에 맞게 설정을 추가하세요.

### vite-env.d.ts 추가

`src/vite-env.d.ts` 파일을 생성하고 아래처럼 작성합니다.

```ts
/// <reference types="vite/client" />
```

### 패키지 설치

`pnpm install`을 실행해 패키지를 설치합니다.

### 스크립트 작성

`src/my-script.tsx` 파일을 아래처럼 생성합니다. 여기서 마운트할 루트 노드의 id가 `my-script-root`인 점을 염두에 두셔야 합니다.

```tsx
import {createRoot} from 'react-dom/client'

// wp_localize_script 함수를 통해 전역변수로 전달되는 값의 타입 지정 
declare global {
    let myScript: {
        name: string
    }
}

createRoot(document.getElementById('my-script-root')!)!.render(
    <p>{myScript.name} 동작 중!</p>
)
```

### `build.rollupOptions.input` 에 엔트리 기입

```typescript
export default defineConfig({
    build: {
        // ...
        rollupOptions: {
            input: [
                // ...
                'src/my-script.tsx', // 예시의 엔트리 추가
            ],
        }
    },
    // ... 
})
```

### 마운트 지점 설정

리액트의 마운트 포인트를 출력하는 코드를 작성합니다.

```html

<div id="my-script-root"></div>
```

### 엔트리 스크립트 삽입

엔트리 스크립트를 삽입하기위해 `ViteScript` 객체를 생성합니다.
이 객체의 생성자는 1개의 연관배열을 입력받습니다. 이것은 스크립트를 위한 설정들이며, 아래를 참고하시기 바랍니다.

- 필수 인자
    - `distBaseUrl`: 문자열. 프로덕션 모드에서 스크립트를 읽어올 기본 주소를 설정합니다.
    - `manifestPath`: 문자열. `manifest.json` 파일의 경로를 입력합니다.
- 선택 인자
    - `devServerURl`: 문자열. 개발서버의 주소를 지정합니다. 생략하면 Vite 기본값인 `http://localhost:5173`을 사용합니다.
    - `isProd`: 불리언. 생략하면 `true`로 인식됩니다.

```php
/**
 * Plugin Name: My Plugin
 *
 * /path/to/wp/wp-content/plugins/my-plugin/index.php (플러그인 메인 파일) 
 */

// ... 생략 ...
$vite = new \Bojaghi\ViteScripts\ViteScript(
    [
        'distBaseUrl'  => plugin_dir_url(__FILE__) . 'dist',
        'isProd'       => 'production' === wp_get_environment_type(),
        'manifestPath' => plugin_dir_path(__FILE__) . 'dist/.vite/manifest.json' 
    ],
);

$vite
    ->add('my-script', 'src/my-script.tsx')
    ->vars('myScript', ['name' => 'My React Script']);
```

`distBaseUrl`은 Vite가 프로덕션용 코드를 만들어내는 곳의 URL 입니다. 기본값으로 Vite는 프로젝트 루트아래 'dist' 디렉토리에
빌드된 스크립트를 보관합니다. 위 예제처럼 URL을 입력합니다.

`manifestPath`는 Vite가 프로덕션 코드를 생성한 후, 생성한 코드에 대한 정보를 기록한 곳입니다.
프로덕션 코드 로딩에 반드시 필요한 파일이므로 정확하게 입력해야 합니다. 기본값으로 'dist/.vite/manifest.json'에 생성되므로,
적절하게 플러그인의 절대 경로를 (URL이 아님!) 지정해 주면 됩니다.

`isProd`는 직접 값을 입력하는 것 보다는 `wp-config.php` 파일에 `WP_ENVIRONMENT_TYPE` 상수를 'production' 또는 다른 값으로
수정하는 것에 의해 결정하게 하는 것이 좋습니다. 일단, 생략하면 `true`입니다.

만약 Vite 개발서버의 도메인 또는 포트를 변경했다면 `devServerUrl` 값을 만드시 지정하십시오.
생략하면 `http://localhost:5173`입니다.

`$vite` 변수가 초기화되었으면 `add()` 메소드로 스크립트를 삽입합니다. 스크립트는 항상 푸터 부분에 들어갑니다.
첫번째 인자로 핸들, 두번째 인자로 프로젝트 루트에서 상대 경로의 엔트리 스크립트의 경로를 입력합니다.

`wp_localize()` 함수를 활용해 데이터를 스크립트로 전달 가능합니다. 이것을 조금 더 편리하게 래핑한 것이
`vars()` 메소드입니다. 인자로 변수 이름과 변수 값을 넣어 주면 됩니다. 엔트리 지점의 `declare global ...`선언을 참고하여
데이터의 형테를 일치시켜 주면 됩니다.

## 예제

본 라이브러리의 `tests/sample-plugin`은 샘플 예제 플러그인입니다.
개발용 워드프레스에 해당 디렉토리를 복사하거나, 심볼릭 링크 처리하여 플러그인을 테스트해 볼 수 있습니다.
