import {cn} from '@/lib/utils'
import Group from './Group'
import AddGroup from './AddGroup'
import {useGlobalContext} from '@/term-merger/context'

export default function GroupsWrap() {
    const {
        state: {
            groupOrders,
            groups,
        },
    } = useGlobalContext()

    return (
        <div
            id="groups-wrap"
            className={cn(
                'sw-ms-1 sw-pb-4', // generic
                'sw-flex-grow',    // flex
            )}
        >
            <ul
                id="groups-list"
                className={cn(
                    'sw-m-0 sw-ps-1', // generic
                )}
            >
                {groupOrders.map((groupId) => {
                    const group = groups.get(groupId)
                    return group && (
                        <li
                            className={cn(
                                'sw-group',
                                'sw-m-1 sw-mb-4 sw-pb-2',                         // generic
                                'sw-border sw-border-gray-400 sw-border-solid', // border
                            )}
                            key={groupId}
                        >
                            <Group group={group} />
                        </li>
                    )
                })}
            </ul>
            <div className="sw-text-center sw-mt-6">
                <AddGroup />
            </div>
        </div>
    )
}

export {GroupsWrap}
