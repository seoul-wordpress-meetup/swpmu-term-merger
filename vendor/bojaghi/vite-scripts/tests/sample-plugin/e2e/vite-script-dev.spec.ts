import {test, expect} from '@playwright/test';

test('Component check and render output', async ({page}) => {
    await page.goto('/')

    // Expect #vite-script-1-root presents.
    await expect(page.locator('#vite-script-1-root')).toHaveCount(1)

    // Expect #vite-script-1-root properly rendered.
    expect(await page.locator('#vite-script-1-root').innerText()).toEqual(
        [
            'Child component: #1 of \'script1-component\'.',
            'Child component: #2 of \'script1-component\'.',
            'Child component: #3 of \'script1-component\'.',
        ].join('\n')
    )

    // Expect #vite-script-2-root presents.
    await expect(page.locator('#vite-script-2-root')).toHaveCount(1)

    // Expect #vite-script-2-root properly rendered.
    expect(await page.locator('#vite-script-2-root').innerText()).toEqual(
        [
            'Child component: #4 of \'script2-component\'.',
            'Child component: #5 of \'script2-component\'.',
            'Child component: #6 of \'script2-component\'.',
        ].join('\n')
    )
})
