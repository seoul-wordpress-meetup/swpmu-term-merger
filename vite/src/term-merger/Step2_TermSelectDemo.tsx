import ComponentContainer from '@/components/ui/ComponentContainer'
import {Card, CardBody, CardBodyDescription, CardFooter, CardHeader, CardHeaderLabel} from '@/components/ui/Card'
import {__} from '@wordpress/i18n'
import {cn} from '@/lib/utils'
import TermBox from '@/components/ui/TermBox'

export default function Step2_TermSelectDemo() {
    return (
        <ComponentContainer className="sw-mb-4">
            <Card>
                <CardHeader>
                    <CardHeaderLabel>
                        {__('Step 2/2: Term Selection & Merge', 'swpmu-term-merger')}
                    </CardHeaderLabel>
                </CardHeader>
                <CardBody>
                    <CardBodyDescription className="">
                        {__('Please choose two or more terms to merge.', 'swpmu-term-merger')}
                    </CardBodyDescription>
                    <Demo_Inner_Raw_Html /> {/* 아직 날것 그대로의 HTML 코드를 사용하고 있음. */}
                </CardBody>
                <CardFooter>
                    <div className="sw-flex sw-justify-between sw-mt-4 sw-mb-2 sw-mx-2">
                        <button
                            className="button button-secondary button-hero"
                        >
                            &laquo; {__('Previous Step', 'swpmu-term-merger')}
                        </button>
                    </div>
                </CardFooter>
            </Card>
        </ComponentContainer>
    )
}


const Demo_Inner_Raw_Html = () => {
    const terms = [
        'Ab quo autem',
        'Aperiam cum tempora',
        'Asperiores id consequuntur',
        'Asperiores sed rerum veniam',
        'Aspernatur assumenda quia',
        'Assumenda enim non',
        'At quia ut',
        'At sunt minima quisquam dolores',
        'Atque a laudantium odio',
        'Aut doloremque non',
        'Aut iure sit ad',
        'Autem consectetur',
        'Autem repellendus illo nemo',
        'Autem sit dolores',
        'Beatae alias rem',
        'Beatae ex voluptatibus nam',
        'Beatae laboriosam',
        'Blanditiis eius',
        'Commodi sunt',
        'Consectetur quae',
        'Consequatur ea possimus',
        'Consequatur nisi iusto',
        'Consequatur repellat numquam',
        'Consequatur voluptatum',
        'Corporis quia et et commodi',
        'Corrupti laboriosam',
        'Corrupti non inventore',
        'Culpa quas reprehenderit mollitia qui',
        'Cumque nihil',
        'Cumque non',
        'Cupiditate sit',
        'Deleniti enim',
        'Deserunt hic in beatae',
        'Dicta et accusamus',
        'Dicta quo saepe',
        'Dignissimos dolorum error',
        'Dignissimos nemo eveniet',
        'Dignissimos voluptas sed dolore eos',
        'Dolor cumque',
        'Dolore accusamus',
        'Dolore doloribus',
        'Dolore minus qui',
        'Dolorem nobis',
        'Dolorem voluptatem illo',
        'Doloremque consequuntur tempora',
        'Dolores magnam et',
        'Doloribus facilis perferendis quis',
        'Dolorum voluptas',
        'Ducimus ad dolor quo',
        'Ea quam facilis',
        'Eaque animi dolorum',
        'Eaque est vero',
        'Eaque quos esse',
        'Earum delectus vero',
        'Eius eum velit',
        'Eius laudantium nobis modi',
        'Eligendi eum iste',
        'Enim et',
        'Enim necessitatibus atque',
        'Eos consectetur mollitia',
        'Eos sed',
        'Eos soluta',
        'Error ut sit',
        'Esse fugiat atque',
        'Esse fugiat dolorum',
        'Est adipisci quis vero ullam',
        'Est consequuntur et',
        'Est deserunt',
        'Est ex',
        'Est porro rerum quam',
        'Est ut repudiandae quia',
        'Et aspernatur quia commodi',
        'Et consequatur',
        'Et consequatur aspernatur',
        'Et et aliquam',
        'Et et consequuntur cumque',
        'Et et expedita',
        'Et in reiciendis accusantium',
        'Et iste ut',
        'Et nihil',
        'Et non ducimus',
        'Et pariatur',
        'Et pariatur dolores sapiente',
        'Et perferendis ut facere corrupti',
        'Et repudiandae quos',
        'Et voluptatem illum',
        'Eum doloribus',
        'Eum impedit ea',
        'Eum quia',
        'Eum voluptas',
        'Eveniet error at aliquam',
        'Excepturi eaque esse fugiat',
        'Exercitationem fugit vel',
        'Exercitationem ipsam',
        'Exercitationem placeat',
        'Expedita est vel voluptatem',
        'Explicabo est odit',
        'Explicabo rerum asperiores',
        'Facilis accusamus ratione',
        'Facilis quos molestiae',
    ]

    return (
        <div
            className={cn(
                'sw-flex sw-flex-row sw-flex-nowrap',
            )}
        >
            <ol
                id="all-terms-list"
                className={cn(
                    'sw-grid sw-grid-cols-3',
                    'sw-list-none sw-m-0 sw-pe-2',
                    'sw-border sw-border-y-0 sw-border-s-0 sw-border-solid sw-border-gray-400',
                )}
            >
                {terms.map((term) => (
                    <li
                        key={term}
                        className=""
                    >
                        <TermBox>
                            {term}
                        </TermBox>
                    </li>
                ))}
            </ol>
            {/* END: all-terms-list */}

            <div
                id="slots-wrap"
                className={cn(
                    'sw-flex-grow',
                    'sw-ms-1 sw-pb-4',
                )}
            >
                <ul
                    id="slots-list"
                    className={cn(
                        'sw-m-0 sw-ps-1',
                    )}
                >
                    <li className={cn(
                        'sw-border sw-border-gray-400 sw-border-solid',
                        'sw-m-1 sw-pb-2',
                    )}>
                        <div>
                            <div className={cn(
                                'sw-p-2',
                                'sw-bg-gray-300',
                            )}>
                                <h3 className="sw-m-0">
                                    Title of the slot - Slot #1
                                </h3>
                            </div>
                            <div className="sw-p-2 sw-text-right">
                                <a href={'#'}
                                   className="sw-no-underline"
                                   title={'Rename the title of this slot'}>Rename</a>
                                {' '}|{' '}
                                <a href={'#'}
                                   className="sw-no-underline sw-text-red-600"
                                   title={'Remove this slot'}>Remove</a>
                            </div>
                        </div>
                        {/* END: Slot's tool area */}
                        <ul className="sw-m-0 sw-p-2">
                            <li
                                className={cn(
                                    'sw-border sw-border-solid sw-border-gray-300',
                                    'sw-m-0 sw-mb-2 sw-p-2',
                                )}
                            >
                                <p className="sw-m-0">
                                    Term A
                                </p>
                                <p className="sw-m-0 sw-text-right">
                                    <a
                                        className="sw-no-underline"
                                        href={'#'}
                                        title={'Move to another slot'}
                                    >
                                        Move to ...
                                    </a>
                                    {' '}|{' '}
                                    <a
                                        className="sw-no-underline sw-text-red-600"
                                        href={'#'}
                                        title={'Remove this term from the slot'}
                                    >
                                        Remove
                                    </a>
                                </p>
                            </li>
                            <li
                                className={cn(
                                    'sw-border sw-border-solid sw-border-gray-300',
                                    'sw-m-0 sw-mb-4 sw-p-2',
                                )}
                            >
                                <p className="sw-m-0">
                                    Term B
                                </p>
                                <p className="sw-m-0 sw-text-right">
                                    <a
                                        className="sw-no-underline"
                                        href={'#'}
                                        title={'Move to another slot'}
                                    >
                                        Move to ...
                                    </a>
                                    {' '}|{' '}
                                    <a
                                        className="sw-no-underline sw-text-red-600"
                                        href={'#'}
                                        title={'Remove this term from the slot'}
                                    >
                                        Remove
                                    </a>
                                </p>
                            </li>
                        </ul>
                        <p className="sw-m-0 sw-text-center">
                            <button
                                className="button button-primary"
                                title={'Merge all terms within this slot'}
                            >Merge ...
                            </button>
                        </p>
                    </li>
                    {/* END: slot #1 */}
                </ul>
                <div className="sw-text-center sw-mt-6">
                    <button
                        className="button button-secondary"
                        title={'Add a new slot'}
                    >Add Slot
                    </button>
                </div>
            </div>
            {/* END: slots-wrap */}
        </div>
    )
}
