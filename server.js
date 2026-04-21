import express from 'express';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const port = process.env.PORT || 3000;

// บอกให้ Express ไปดึงไฟล์จากโฟลเดอร์ dist (ซึ่งมาจากการรัน npm run build)
app.use(express.static(path.join(__dirname, 'dist')));

// ถ้าเข้าหน้าแรก ให้ส่งไฟล์ index.html ใน dist ออกไป
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'dist', 'index.html'));
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});