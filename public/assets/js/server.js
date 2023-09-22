const express = require('express');
const app = express();
const port = 3000;

const { Sequelize, DataTypes } = require('sequelize');

const sequelize = new Sequelize(process.env.DATABASE_NAME, process.env.DATABASE_USERNAME, process.env.DATABASE_PASSWORD, {
    host: process.env.DATABASE_HOST,
    dialect: 'mysql',
  });

const Product = sequelize.define('Product', {
  productTitle: {
    type: DataTypes.STRING,
  },
});

app.use(express.json());

app.get('/filter-products/:criteria', async (req, res) => {
  const criteria = req.params.criteria;

  try {
    const filteredProducts = await Product.findAll({
      where: {
        productTitle: {
          [Sequelize.Op.iLike]: `%${criteria}%`,
        },
      },
    });

    res.json(filteredProducts);
  } catch (error) {
    console.error('Error:', error);
    res.status(500).json({ error: 'Internal Server Error' });
  }
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});

