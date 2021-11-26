import * as React from 'react';
import PropTypes from 'prop-types';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Typography from '@mui/material/Typography';
import Box from '@mui/material/Box';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import {useEffect, useState} from "react";
import {Button} from "@mui/material";

const DATA_WEB_PATH = "/food/food.json";

function loadData(path) {
  return fetch(path)
    .then(response => response.json());
}

function renderFoodTable(data, config) {
  return (
    <TableContainer component={Paper}>
      <Table sx={{ minWidth: 650 }}>
        <TableHead>
          <TableRow>
            <TableCell>Меню</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {data.map((row) => (
            <TableRow
              key={row.name}
              sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
            >
              <TableCell>
                <Button href={`${config.path}/${row.filename}`}>{row.filename}</Button>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
}

function TabPanel(props) {
  const { children, value, index, ...other } = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`simple-tabpanel-${index}`}
      aria-labelledby={`simple-tab-${index}`}
      {...other}
    >
      {value === index && (
        <Box sx={{ p: 3 }}>
          <Typography>{children}</Typography>
        </Box>
      )}
    </div>
  );
}

TabPanel.propTypes = {
  children: PropTypes.node,
  index: PropTypes.number.isRequired,
  value: PropTypes.number.isRequired,
};

export default function FoodTabs() {
  const [value, setValue] = useState(0);
  const [ data, setData ] = useState({
    config: {
      path: "/"
    },
    data: {
      ss: [],
      sm: []
    }
  });

  useEffect(() => {
    loadData(DATA_WEB_PATH)
      .then(data => setData(data));
  }, [])

  const handleChange = (event, newValue) => {
    setValue(newValue);
  };

  const { data: { ss, sm }, config } = data;

  return (
    <Box sx={{ width: '100%' }}>
      <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
        <Tabs value={value} onChange={handleChange}>
          <Tab label="Начальная школа" />
          <Tab label="Средняя школа" />
        </Tabs>
      </Box>
      <TabPanel value={value} index={0}>
        {renderFoodTable(sm, config)}
      </TabPanel>
      <TabPanel value={value} index={1}>
        {renderFoodTable(ss, config)}
      </TabPanel>
    </Box>
  );
}
