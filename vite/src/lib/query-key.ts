const queryKey = {
    defaults: ['swm-term-merger'],
    getTerms: (taxonomy: string) => [...queryKey.defaults, 'get-terms', taxonomy],
} as const

export default queryKey
