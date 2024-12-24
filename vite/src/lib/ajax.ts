import axios, {type AxiosRequestConfig, AxiosResponse} from 'axios'
import {AjaxAction, AjaxResponse, Term, WPError} from '@/types'

const setup: {
    actions: { [key: string]: AjaxAction }
    endpoint: string
} = {
    actions: {},
    endpoint: '',
}

const adminAjax = {
    /**
     * getTerms
     *
     * Get term objects by given taxonomy
     *
     * @param taxonomy
     */
    getTerms: async (taxonomy: string) => adminAjaxGet<Term[]>('getTerms', {taxonomy}),

    /**
     * mergeTerms
     *
     * Execute term merge
     *
     * @param head
     * @param target
     * @param taxonomy
     */
    mergeTerms: async (head: number, target: number[], taxonomy: string) => {
        return adminAjaxPost('mergeTerms', {head, target, taxonomy})
    },
}

async function adminAjaxGet<T>(action: string, params: any = {}) {
    return adminAjaxRequest<T>(action, 'get', params)
}

async function adminAjaxPost<T>(action: string, data: any = {}) {
    return adminAjaxRequest<T>(action, 'post', data)
}

async function adminAjaxRequest<T>(
    action: string,
    method: string,
    extraData: any = {},
    extraRequest: AxiosRequestConfig = {},
) {
    method = method.toUpperCase()

    const e = extractSetup(action)

    if (e.action === '') {
        throw `Invalid action. '${action}' is not provided by the PHP script.`
    }

    const payload = {
        action: e.action,
        [e.key]: e.nonce,
        ...extraData,
    }

    const r = await axios({
        method,
        url: setup.endpoint,
        ...('POST' === method ? {
            data: payload,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        } : {
            params: payload,
        }),
        ...extraRequest,
    })

    return checkAjaxResponse<T>(r)
}

function extractSetup(key: string): AjaxAction {
    return setup.actions[key] ?? {
        action: '',
        key: '',
        nonce: '',
    }
}

function checkAjaxResponse<T = any>(
    response: AxiosResponse<AjaxResponse<T>>,
    errorHandler: ((data: WPError[]) => void) | null = null,
): T | undefined {
    const {success, data} = response.data

    if (success) {
        return data
    }

    if (errorHandler) {
        errorHandler(data)
    }
}


function initAdminAjax(actions: { [key: string]: AjaxAction }, endpoint: string) {
    setup.actions = actions
    setup.endpoint = endpoint
}

export {
    adminAjax,
    initAdminAjax,
}
