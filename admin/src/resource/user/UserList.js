import React from 'react';
import {
    List,
    Pagination,
    Filter,
    Datagrid,
    TextField,
    BooleanField,
    TextInput
} from 'react-admin';
import config from '../../config';

const UserFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Search by username" source="username" alwaysOn />
        <TextInput label="Search by name" source="fullName" alwaysOn />
    </Filter>
);

const UserPagination = (props) => (
    <Pagination rowsPerPageOptions={[]} {...props} />
);

export default props => (
    <List {...props} bulkActionButtons={false} perPage={config.perPage} filters={<UserFilter/>} pagination={<UserPagination/>}>
        <Datagrid>
            <TextField source="username" label="Username"/>
            <TextField source="fullName" label="Name"/>
            <BooleanField source="enabled" label="Is enabled"/>
        </Datagrid>
    </List>
);
