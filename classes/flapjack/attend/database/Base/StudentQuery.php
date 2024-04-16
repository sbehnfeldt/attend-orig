<?php

namespace flapjack\attend\database\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use flapjack\attend\database\Student as ChildStudent;
use flapjack\attend\database\StudentQuery as ChildStudentQuery;
use flapjack\attend\database\Map\StudentTableMap;

/**
 * Base class that represents a query for the `students` table.
 *
 * @method     ChildStudentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStudentQuery orderByFamilyName($order = Criteria::ASC) Order by the family_name column
 * @method     ChildStudentQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     ChildStudentQuery orderByEnrolled($order = Criteria::ASC) Order by the enrolled column
 * @method     ChildStudentQuery orderByClassroomId($order = Criteria::ASC) Order by the classroom_id column
 *
 * @method     ChildStudentQuery groupById() Group by the id column
 * @method     ChildStudentQuery groupByFamilyName() Group by the family_name column
 * @method     ChildStudentQuery groupByFirstName() Group by the first_name column
 * @method     ChildStudentQuery groupByEnrolled() Group by the enrolled column
 * @method     ChildStudentQuery groupByClassroomId() Group by the classroom_id column
 *
 * @method     ChildStudentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStudentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStudentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStudentQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStudentQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStudentQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStudentQuery leftJoinClassroom($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classroom relation
 * @method     ChildStudentQuery rightJoinClassroom($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classroom relation
 * @method     ChildStudentQuery innerJoinClassroom($relationAlias = null) Adds a INNER JOIN clause to the query using the Classroom relation
 *
 * @method     ChildStudentQuery joinWithClassroom($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Classroom relation
 *
 * @method     ChildStudentQuery leftJoinWithClassroom() Adds a LEFT JOIN clause and with to the query using the Classroom relation
 * @method     ChildStudentQuery rightJoinWithClassroom() Adds a RIGHT JOIN clause and with to the query using the Classroom relation
 * @method     ChildStudentQuery innerJoinWithClassroom() Adds a INNER JOIN clause and with to the query using the Classroom relation
 *
 * @method     ChildStudentQuery leftJoinSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Schedule relation
 * @method     ChildStudentQuery rightJoinSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Schedule relation
 * @method     ChildStudentQuery innerJoinSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the Schedule relation
 *
 * @method     ChildStudentQuery joinWithSchedule($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Schedule relation
 *
 * @method     ChildStudentQuery leftJoinWithSchedule() Adds a LEFT JOIN clause and with to the query using the Schedule relation
 * @method     ChildStudentQuery rightJoinWithSchedule() Adds a RIGHT JOIN clause and with to the query using the Schedule relation
 * @method     ChildStudentQuery innerJoinWithSchedule() Adds a INNER JOIN clause and with to the query using the Schedule relation
 *
 * @method     \flapjack\attend\database\ClassroomQuery|\flapjack\attend\database\ScheduleQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildStudent|null findOne(?ConnectionInterface $con = null) Return the first ChildStudent matching the query
 * @method     ChildStudent findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildStudent matching the query, or a new ChildStudent object populated from the query conditions when no match is found
 *
 * @method     ChildStudent|null findOneById(int $id) Return the first ChildStudent filtered by the id column
 * @method     ChildStudent|null findOneByFamilyName(string $family_name) Return the first ChildStudent filtered by the family_name column
 * @method     ChildStudent|null findOneByFirstName(string $first_name) Return the first ChildStudent filtered by the first_name column
 * @method     ChildStudent|null findOneByEnrolled(boolean $enrolled) Return the first ChildStudent filtered by the enrolled column
 * @method     ChildStudent|null findOneByClassroomId(int $classroom_id) Return the first ChildStudent filtered by the classroom_id column
 *
 * @method     ChildStudent requirePk($key, ?ConnectionInterface $con = null) Return the ChildStudent by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOne(?ConnectionInterface $con = null) Return the first ChildStudent matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudent requireOneById(int $id) Return the first ChildStudent filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByFamilyName(string $family_name) Return the first ChildStudent filtered by the family_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByFirstName(string $first_name) Return the first ChildStudent filtered by the first_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByEnrolled(boolean $enrolled) Return the first ChildStudent filtered by the enrolled column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByClassroomId(int $classroom_id) Return the first ChildStudent filtered by the classroom_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudent[]|Collection find(?ConnectionInterface $con = null) Return ChildStudent objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildStudent> find(?ConnectionInterface $con = null) Return ChildStudent objects based on current ModelCriteria
 *
 * @method     ChildStudent[]|Collection findById(int|array<int> $id) Return ChildStudent objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildStudent> findById(int|array<int> $id) Return ChildStudent objects filtered by the id column
 * @method     ChildStudent[]|Collection findByFamilyName(string|array<string> $family_name) Return ChildStudent objects filtered by the family_name column
 * @psalm-method Collection&\Traversable<ChildStudent> findByFamilyName(string|array<string> $family_name) Return ChildStudent objects filtered by the family_name column
 * @method     ChildStudent[]|Collection findByFirstName(string|array<string> $first_name) Return ChildStudent objects filtered by the first_name column
 * @psalm-method Collection&\Traversable<ChildStudent> findByFirstName(string|array<string> $first_name) Return ChildStudent objects filtered by the first_name column
 * @method     ChildStudent[]|Collection findByEnrolled(boolean|array<boolean> $enrolled) Return ChildStudent objects filtered by the enrolled column
 * @psalm-method Collection&\Traversable<ChildStudent> findByEnrolled(boolean|array<boolean> $enrolled) Return ChildStudent objects filtered by the enrolled column
 * @method     ChildStudent[]|Collection findByClassroomId(int|array<int> $classroom_id) Return ChildStudent objects filtered by the classroom_id column
 * @psalm-method Collection&\Traversable<ChildStudent> findByClassroomId(int|array<int> $classroom_id) Return ChildStudent objects filtered by the classroom_id column
 *
 * @method     ChildStudent[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildStudent> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class StudentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\StudentQuery object.
     *
     * @param string $dbName The database name
     * @param string $modelName The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'attend', $modelName = '\\flapjack\\attend\\database\\Student', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStudentQuery object.
     *
     * @param string $modelAlias The alias of a model in the query
     * @param Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStudentQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildStudentQuery) {
            return $criteria;
        }
        $query = new ChildStudentQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildStudent|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StudentTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = StudentTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStudent A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, family_name, first_name, enrolled, classroom_id FROM students WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildStudent $obj */
            $obj = new ChildStudent();
            $obj->hydrate($row);
            StudentTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildStudent|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param array $keys Primary keys to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return Collection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ?ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        $this->addUsingAlias(StudentTableMap::COL_ID, $key, Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array|int $keys The list of primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        $this->addUsingAlias(StudentTableMap::COL_ID, $keys, Criteria::IN);

        return $this;
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterById($id = null, ?string $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(StudentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(StudentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(StudentTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the family_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFamilyName('fooValue');   // WHERE family_name = 'fooValue'
     * $query->filterByFamilyName('%fooValue%', Criteria::LIKE); // WHERE family_name LIKE '%fooValue%'
     * $query->filterByFamilyName(['foo', 'bar']); // WHERE family_name IN ('foo', 'bar')
     * </code>
     *
     * @param string|string[] $familyName The value to use as filter.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByFamilyName($familyName = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($familyName)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(StudentTableMap::COL_FAMILY_NAME, $familyName, $comparison);

        return $this;
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%', Criteria::LIKE); // WHERE first_name LIKE '%fooValue%'
     * $query->filterByFirstName(['foo', 'bar']); // WHERE first_name IN ('foo', 'bar')
     * </code>
     *
     * @param string|string[] $firstName The value to use as filter.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(StudentTableMap::COL_FIRST_NAME, $firstName, $comparison);

        return $this;
    }

    /**
     * Filter the query on the enrolled column
     *
     * Example usage:
     * <code>
     * $query->filterByEnrolled(true); // WHERE enrolled = true
     * $query->filterByEnrolled('yes'); // WHERE enrolled = true
     * </code>
     *
     * @param bool|string $enrolled The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByEnrolled($enrolled = null, ?string $comparison = null)
    {
        if (is_string($enrolled)) {
            $enrolled = in_array(strtolower($enrolled), array('false', 'off', '-', 'no', 'n', '0', ''), true) ? false : true;
        }

        $this->addUsingAlias(StudentTableMap::COL_ENROLLED, $enrolled, $comparison);

        return $this;
    }

    /**
     * Filter the query on the classroom_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassroomId(1234); // WHERE classroom_id = 1234
     * $query->filterByClassroomId(array(12, 34)); // WHERE classroom_id IN (12, 34)
     * $query->filterByClassroomId(array('min' => 12)); // WHERE classroom_id > 12
     * </code>
     *
     * @see       filterByClassroom()
     *
     * @param mixed $classroomId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByClassroomId($classroomId = null, ?string $comparison = null)
    {
        if (is_array($classroomId)) {
            $useMinMax = false;
            if (isset($classroomId['min'])) {
                $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classroomId['max'])) {
                $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Classroom object
     *
     * @param \flapjack\attend\database\Classroom|ObjectCollection $classroom The related object(s) to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByClassroom($classroom, ?string $comparison = null)
    {
        if ($classroom instanceof \flapjack\attend\database\Classroom) {
            return $this
                ->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroom->getId(), $comparison);
        } elseif ($classroom instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroom->toKeyValue('PrimaryKey', 'Id'), $comparison);

            return $this;
        } else {
            throw new PropelException('filterByClassroom() only accepts arguments of type \flapjack\attend\database\Classroom or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Classroom relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinClassroom(?string $relationAlias = null, ?string $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Classroom');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Classroom');
        }

        return $this;
    }

    /**
     * Use the Classroom relation Classroom object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\ClassroomQuery A secondary query class using the current class as primary query
     */
    public function useClassroomQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinClassroom($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Classroom', '\flapjack\attend\database\ClassroomQuery');
    }

    /**
     * Use the Classroom relation Classroom object
     *
     * @param callable(\flapjack\attend\database\ClassroomQuery):\flapjack\attend\database\ClassroomQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withClassroomQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::LEFT_JOIN
    ) {
        $relatedQuery = $this->useClassroomQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Classroom table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\ClassroomQuery The inner query object of the EXISTS statement
     */
    public function useClassroomExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\ClassroomQuery */
        $q = $this->useExistsQuery('Classroom', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Classroom table for a NOT EXISTS query.
     *
     * @see useClassroomExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\ClassroomQuery The inner query object of the NOT EXISTS statement
     */
    public function useClassroomNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\ClassroomQuery */
        $q = $this->useExistsQuery('Classroom', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Classroom table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\ClassroomQuery The inner query object of the IN statement
     */
    public function useInClassroomQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\ClassroomQuery */
        $q = $this->useInQuery('Classroom', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Classroom table for a NOT IN query.
     *
     * @see useClassroomInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\ClassroomQuery The inner query object of the NOT IN statement
     */
    public function useNotInClassroomQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\ClassroomQuery */
        $q = $this->useInQuery('Classroom', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Schedule object
     *
     * @param \flapjack\attend\database\Schedule|ObjectCollection $schedule the related object to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterBySchedule($schedule, ?string $comparison = null)
    {
        if ($schedule instanceof \flapjack\attend\database\Schedule) {
            $this
                ->addUsingAlias(StudentTableMap::COL_ID, $schedule->getStudentId(), $comparison);

            return $this;
        } elseif ($schedule instanceof ObjectCollection) {
            $this
                ->useScheduleQuery()
                ->filterByPrimaryKeys($schedule->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException('filterBySchedule() only accepts arguments of type \flapjack\attend\database\Schedule or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Schedule relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinSchedule(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Schedule');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Schedule');
        }

        return $this;
    }

    /**
     * Use the Schedule relation Schedule object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\ScheduleQuery A secondary query class using the current class as primary query
     */
    public function useScheduleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Schedule', '\flapjack\attend\database\ScheduleQuery');
    }

    /**
     * Use the Schedule relation Schedule object
     *
     * @param callable(\flapjack\attend\database\ScheduleQuery):\flapjack\attend\database\ScheduleQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withScheduleQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useScheduleQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Schedule table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\ScheduleQuery The inner query object of the EXISTS statement
     */
    public function useScheduleExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\ScheduleQuery */
        $q = $this->useExistsQuery('Schedule', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Schedule table for a NOT EXISTS query.
     *
     * @see useScheduleExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\ScheduleQuery The inner query object of the NOT EXISTS statement
     */
    public function useScheduleNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\ScheduleQuery */
        $q = $this->useExistsQuery('Schedule', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Schedule table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\ScheduleQuery The inner query object of the IN statement
     */
    public function useInScheduleQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\ScheduleQuery */
        $q = $this->useInQuery('Schedule', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Schedule table for a NOT IN query.
     *
     * @see useScheduleInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\ScheduleQuery The inner query object of the NOT IN statement
     */
    public function useNotInScheduleQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\ScheduleQuery */
        $q = $this->useInQuery('Schedule', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param ChildStudent $student Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($student = null)
    {
        if ($student) {
            $this->addUsingAlias(StudentTableMap::COL_ID, $student->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the students table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StudentTableMap::clearInstancePool();
            StudentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StudentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StudentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StudentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
