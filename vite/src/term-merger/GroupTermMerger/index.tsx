import {cn} from '@/lib/utils'
import GroupsWrap from './groups/GroupsWrap'
import AllTermsList from './terms/AllTermsList'

export default function GroupTermMerger() {
    return (
        <div
            className={cn(
                'sw-flex sw-flex-row sw-flex-nowrap', // flex
            )}
        >
            <AllTermsList />
            <GroupsWrap />
        </div>
    )
}
