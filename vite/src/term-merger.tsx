import {initAdminAjax} from '@/lib/ajax'
import TermMerger, {Props} from '@/term-merger/TermMerger'
import {AjaxAction} from '@/types'
import {createRoot} from 'react-dom/client'
import '@/globals.css'
import {setLocaleData} from '@wordpress/i18n'
import type {LocaleData} from '@wordpress/i18n/build-types/create-i18n'

declare global {
    const swmTermMerger: {
        actions: { [key: string]: AjaxAction }
        endpoint: string
    } & Props

    const wp: {
        i18n: {
            getLocaleData(locale: string): LocaleData
        }
    }
}

const {actions, endpoint, initialState} = swmTermMerger

initAdminAjax(actions, endpoint)
setLocaleData(wp.i18n.getLocaleData('swm-term-merger'), 'swm-term-merger')

createRoot(document.getElementById('term-merger-root')!)!.render(
    <TermMerger initialState={initialState} />,
)
