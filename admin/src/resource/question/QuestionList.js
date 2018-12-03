import React from 'react';
import {
    List,
    Pagination,
    Datagrid,
    TextField,
    TextInput,
    Filter,
    EditButton } from 'react-admin';
import config from '../../config';

const QuestionFilter = (props) => (
    <Filter {...props}>
        <TextInput label="Search" source="content" alwaysOn />
    </Filter>
);

const QuestionPagination = (props) => (
    <Pagination rowsPerPageOptions={[]} {...props} />
);

export default props => (
    <List {...props} perPage={config.perPage} filters={<QuestionFilter/>} sort={{ field: 'updatedAt', order: 'DESC' }} pagination={<QuestionPagination/>}>
        <Datagrid>
            <TextField source="content" label="Content"/>
            <EditButton />
        </Datagrid>
    </List>
);
