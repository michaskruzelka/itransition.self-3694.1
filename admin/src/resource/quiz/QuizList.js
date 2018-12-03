import React from 'react';
import {
    List,
    Pagination,
    Filter,
    TextInput,
    Datagrid,
    TextField,
    DateField,
    BooleanField,
    ReferenceField,
    EditButton } from 'react-admin';
import config from '../../config';

const QuizFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Search" source="name" alwaysOn />
    </Filter>
);

const QuizPagination = (props) => (
    <Pagination rowsPerPageOptions={[]} {...props} />
);

export default props => (
    <List {...props} perPage={config.perPage} filters={<QuizFilter/>} pagination={<QuizPagination/>}>
        <Datagrid>
            <TextField source="name" label="Name"/>
            <ReferenceField source="author" reference="users" linkType={false}>
                <TextField source="fullName" />
            </ReferenceField>
            <BooleanField source="isActive" label="Is Active" sortable={false}/>
            <DateField source="createdAt" label="Created At" showTime sortable={false}/>
            <EditButton />
        </Datagrid>
    </List>
);
