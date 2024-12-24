# 샘플 플러그인

## 사용법

1. wp-config.php 상수 설정
   - 개발 모드로 사용하려면 `define( 'WP_ENVIRONMENT_TYPE', 'development' );`
   - 프로적션 모드로 사용하려면 `define( 'WP_ENVIRONMENT_TYPE', 'production' );`
2. `pnpm install`
3. 개발 서버 실행: `pnpm run dev`
4. 스크립트 빌드: `pnpm run build`
5. 워드프레스 관리자 페이지 > 'Vite Scripts' 메뉴 클릭
6. 코포넌트 동작을 확인하세요.
