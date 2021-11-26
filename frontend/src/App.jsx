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


function renderFoodTable(data) {
  console.log(data);

  function createData(name) {
    return { name };
  }

  const rows = [
    createData('Frozen yoghurt'),
    createData('Ice cream sandwich'),
  ];

  return (
    <TableContainer component={Paper}>
      <Table sx={{ minWidth: 650 }} aria-label="simple table">
        <TableHead>
          <TableRow>
            <TableCell>Дата</TableCell>
            <TableCell>Меню</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {rows.map((row) => (
            <TableRow
              key={row.name}
              sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
            >
              <TableCell>{row.name}</TableCell>
              <TableCell>{row.name}</TableCell>
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
    setTimeout(() => {
      setData(
        {
          config: {
            path: "/web/path/to/files/"
          },
          data: {
            ss: [
              {
                filename: "2021-11-26-ss.xlsx",
                ctime: {
                  date: "2021-11-26 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "ss",
                md5: "6d7fce9fee471194aa8b5b6e47267f03"
              },
              {
                filename: "2021-11-24-ss.xlsx",
                ctime: {
                  date: "2021-11-24 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "ss",
                md5: "b026324c6904b2a9cb4b88d6d61c81d1"
              },
              {
                filename: "2021-11-23-ss.xlsx",
                ctime: {
                  date: "2021-11-23 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "ss",
                md5: "6d7fce9fee471194aa8b5b6e47267f03"
              }
            ],
            sm: [
              {
                filename: "2021-11-26-sm.xlsx",
                ctime: {
                  date: "2021-11-26 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "sm",
                md5: "6d7fce9fee471194aa8b5b6e47267f03"
              },
              {
                filename: "2021-11-24-sm.xlsx",
                ctime: {
                  date: "2021-11-24 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "sm",
                md5: "b026324c6904b2a9cb4b88d6d61c81d1"
              },
              {
                filename: "2021-11-23-sm.xlsx",
                ctime: {
                  date: "2021-11-23 00:00:00.000000",
                  timezone_type: 1,
                  timezone: "+00:00"
                },
                flag: "sm",
                md5: "6d7fce9fee471194aa8b5b6e47267f03"
              }
            ]
          }
        }
      );
    }, 1000);
  }, [])

  const handleChange = (event, newValue) => {
    setValue(newValue);
  };

  const { data: { ss, sm } } = data;

  return (
    <Box sx={{ width: '100%' }}>
      <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
        <Tabs value={value} onChange={handleChange}>
          <Tab label="Начальная школа" />
          <Tab label="Средняя школа" />
        </Tabs>
      </Box>
      <TabPanel value={value} index={0}>
        {renderFoodTable(sm)}
      </TabPanel>
      <TabPanel value={value} index={1}>
        {renderFoodTable(ss)}
      </TabPanel>
    </Box>
  );
}
